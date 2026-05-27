<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permissions ────────────────────────────────────────────────────────

        $permissions = [
            // Movies
            'view movies',
            'create movies',
            'edit movies',
            'delete movies',
            'upload movie videos',
            'reprocess movies',

            // Series
            'view series',
            'create series',
            'edit series',
            'delete series',
            'upload series videos',

            // Collections
            'view collections',
            'manage collections',

            // Coming Soon
            'view coming soon',
            'manage coming soon',

            // Misc content
            'manage subtitles',
            'manage posters',
            'convert content',

            // Users
            'view users',
            'ban users',
            'manage user subscriptions',
            'assign permissions',

            // Finance
            'view payments',
            'view packages',
            'manage packages',

            // Support
            'view contacts',
            'manage contacts',

            // Operations
            'view analytics',
            'view encoding status',
            'send test emails',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        // ── Roles ──────────────────────────────────────────────────────────────

        $contentManager = Role::firstOrCreate(['name' => 'content-manager']);
        $contentManager->syncPermissions([
            'view movies', 'create movies', 'edit movies', 'delete movies',
            'upload movie videos', 'reprocess movies',
            'view series', 'create series', 'edit series', 'delete series',
            'upload series videos',
            'view collections', 'manage collections',
            'view coming soon', 'manage coming soon',
            'manage subtitles', 'manage posters', 'convert content',
            'view encoding status',
        ]);

        $financeManager = Role::firstOrCreate(['name' => 'finance-manager']);
        $financeManager->syncPermissions([
            'view payments',
            'view packages', 'manage packages',
            'view users', 'manage user subscriptions',
        ]);

        $supportManager = Role::firstOrCreate(['name' => 'support-manager']);
        $supportManager->syncPermissions([
            'view users', 'ban users',
            'view contacts', 'manage contacts',
        ]);

        $analyst = Role::firstOrCreate(['name' => 'analyst']);
        $analyst->syncPermissions([
            'view analytics',
            'view encoding status',
        ]);
    }
}
