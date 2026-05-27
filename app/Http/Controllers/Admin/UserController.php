<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SubscriptionUpdated;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->with([
                'userSubscriptions' => fn ($q) => $q->with('package')->latest('starts_at')->limit(1),
                'roles',
            ])
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();

        return view('pages.admin.users.index', compact('users', 'roles'));
    }

    public function ban(User $user)
    {
        abort_if($user->is(auth()->user()), 403, 'You cannot ban yourself.');
        abort_if($user->isAdmin(), 403, 'Admins cannot be banned.');

        $user->update(['banned_at' => now()]);

        return back()->with('success', "\"{$user->name}\" has been banned.");
    }

    public function unban(User $user)
    {
        $user->update(['banned_at' => null]);

        return back()->with('success', "\"{$user->name}\" has been unbanned.");
    }

    public function extendSubscription(Request $request, User $user)
    {
        $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:3650'],
        ]);

        $days = (int) $request->days;

        $subscription = $user->userSubscriptions()->latest('starts_at')->first();

        if (!$subscription) {
            return back()->with('error', "\"{$user->name}\" has no subscription to extend.");
        }

        $base = ($subscription->expires_at && $subscription->expires_at->isFuture())
            ? $subscription->expires_at
            : now();

        $subscription->update([
            'status'     => 'active',
            'expires_at' => $base->addDays($days),
        ]);

        $subscription->load('package');
        Mail::to($user->email, $user->name)
            ->queue(new SubscriptionUpdated($user, $subscription, 'extended', $days));

        return back()->with('success', "\"{$user->name}\"'s subscription extended by {$days} day(s).");
    }

    public function permissions(User $user)
    {
        abort_if($user->isAdmin(), 403, 'Super-admins have all permissions by default.');

        $allPermissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return match (true) {
                str_contains($p->name, 'movie')            => 'Movies',
                str_contains($p->name, 'series')           => 'Series',
                str_contains($p->name, 'collection')       => 'Collections',
                str_contains($p->name, 'coming soon')      => 'Coming Soon',
                str_contains($p->name, 'subtitle')
                    || str_contains($p->name, 'poster')
                    || str_contains($p->name, 'convert')   => 'Content (Misc)',
                str_contains($p->name, 'user')
                    || str_contains($p->name, 'ban')
                    || str_contains($p->name, 'permission') => 'Users',
                str_contains($p->name, 'payment')
                    || str_contains($p->name, 'package')   => 'Finance',
                str_contains($p->name, 'contact')          => 'Support',
                default                                    => 'Operations',
            };
        });

        $roles               = Role::orderBy('name')->get();
        $userPermissions     = $user->getPermissionNames()->toArray();
        $userRole            = $user->roles->first()?->name;

        return view('pages.admin.users.permissions', compact(
            'user', 'allPermissions', 'roles', 'userPermissions', 'userRole'
        ));
    }

    public function assignRole(Request $request, User $user)
    {
        abort_if($user->isAdmin(), 403, 'Admins cannot be assigned staff roles.');
        abort_if($user->is(auth()->user()), 403, 'You cannot modify your own role.');

        $request->validate([
            'role' => ['nullable', 'string', 'exists:roles,name'],
        ]);

        $user->syncRoles($request->role ? [$request->role] : []);

        $message = $request->role
            ? "Assigned role \"" . str_replace('-', ' ', $request->role) . "\" to {$user->name}."
            : "Removed role from {$user->name}.";

        return back()->with('success', $message);
    }

    public function syncPermissions(Request $request, User $user)
    {
        abort_if($user->isAdmin(), 403, 'Super-admins have all permissions by default.');
        abort_if($user->is(auth()->user()), 403, 'You cannot modify your own permissions.');

        $request->validate([
            'role'        => ['nullable', 'string', 'exists:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        // Sync role (one role at a time; remove old roles first)
        $user->syncRoles($request->role ? [$request->role] : []);

        // Sync individual permissions (direct, on top of role permissions)
        $user->syncPermissions($request->permissions ?? []);

        return redirect()
            ->route('admin.users.permissions', $user)
            ->with('success', "Permissions updated for \"{$user->name}\".");
    }
}
