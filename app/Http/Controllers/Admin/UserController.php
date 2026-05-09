<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('pages.admin.users.index', compact('users'));
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
}
