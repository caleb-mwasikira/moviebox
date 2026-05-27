<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['permissions', 'users'])->orderBy('name')->get();
        return view('pages.admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $allPermissions = $this->groupedPermissions();
        return view('pages.admin.roles.create', compact('allPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:100', 'unique:roles,name'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->name}\" created.");
    }

    public function edit(Role $role)
    {
        $allPermissions  = $this->groupedPermissions();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('pages.admin.roles.edit', compact('role', 'allPermissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:100', 'unique:roles,name,' . $role->id],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->name}\" updated.");
    }

    public function destroy(Role $role)
    {
        $count = $role->users()->count();
        if ($count > 0) {
            return back()->with('error', "Cannot delete \"{$role->name}\" — {$count} user(s) are assigned to it.");
        }

        $name = $role->name;
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$name}\" deleted.");
    }

    private function groupedPermissions(): \Illuminate\Support\Collection
    {
        return Permission::orderBy('name')->get()->groupBy(function ($p) {
            return match (true) {
                str_contains($p->name, 'movie')                                   => 'Movies',
                str_contains($p->name, 'series')                                  => 'Series',
                str_contains($p->name, 'collection')                              => 'Collections',
                str_contains($p->name, 'coming soon')                             => 'Coming Soon',
                str_contains($p->name, 'subtitle')
                    || str_contains($p->name, 'poster')
                    || str_contains($p->name, 'convert')                          => 'Content (Misc)',
                str_contains($p->name, 'user')
                    || str_contains($p->name, 'ban')
                    || str_contains($p->name, 'permission')                       => 'Users',
                str_contains($p->name, 'payment')
                    || str_contains($p->name, 'package')                          => 'Finance',
                str_contains($p->name, 'contact')                                 => 'Support',
                default                                                           => 'Operations',
            };
        });
    }
}
