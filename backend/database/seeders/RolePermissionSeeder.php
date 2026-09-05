<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class RolePermissionSeeder extends Seeder
{
    // ── Platform (Super Admin only) ───────────────────────────────────────────
    private const PLATFORM_PERMISSIONS = [
        'dashboard'  => ['view'],
        'roles'         => ['view', 'create', 'edit', 'delete', 'assign'],
        'users'         => ['view', 'create', 'edit', 'delete'],
    ];

    // ── Site content: one permission set per section of iqbolshoh.uz ──────────
    private const CONTENT_PERMISSIONS = [
        'projects'      => ['view', 'create', 'edit', 'delete'],
        'services'      => ['view', 'create', 'edit', 'delete'],
        'tech-stacks'   => ['view', 'create', 'edit', 'delete'],
        'stats'         => ['view', 'create', 'edit', 'delete'],
        'highlights'    => ['view', 'create', 'edit', 'delete'],
        'journeys'      => ['view', 'create', 'edit', 'delete'],
        'beyonds'       => ['view', 'create', 'edit', 'delete'],
        'process-steps' => ['view', 'create', 'edit', 'delete'],
        'settings'      => ['view', 'edit'],
        'messages'      => ['view', 'delete'],
    ];

    // ── Plan: the personal planning system, owner only ────────────────────────
    private const PLAN_PERMISSIONS = [
        'goals'         => ['view', 'create', 'edit', 'delete'],
        'plans'         => ['view', 'create', 'edit', 'delete'],
        'calendar'      => ['view'],
        'analytics'     => ['view'],
        'forecast'      => ['view'],
        'interruptions' => ['view', 'create', 'edit', 'delete'],
        'notifications' => ['view', 'delete', 'retry'],
        'plan-settings' => ['view', 'edit'],
    ];

    // ── Finance: the owner's own money, owner only ────────────────────────────
    private const FINANCE_PERMISSIONS = [
        'finance'            => ['view'],
        'transactions'       => ['view', 'create', 'edit', 'delete'],
        'finance-categories' => ['view', 'create', 'edit', 'delete'],
        'finance-settings'   => ['view', 'edit'],
    ];

    // ── Time: where the owner's own day went, owner only ──────────────────────
    private const ACTIVITY_PERMISSIONS = [
        'activities'            => ['view'],
        'activities-entries'    => ['view', 'create', 'edit', 'delete'],
        'activities-categories' => ['view', 'create', 'edit', 'delete'],
    ];

    // ── Manager: edits the site content, but cannot delete or touch accounts ──
    private const MANAGER_PERMISSIONS = [
        'dashboard'     => ['view'],
        'projects'      => ['view', 'create', 'edit'],
        'services'      => ['view', 'create', 'edit'],
        'tech-stacks'   => ['view', 'create', 'edit'],
        'stats'         => ['view', 'create', 'edit'],
        'highlights'    => ['view', 'create', 'edit'],
        'journeys'      => ['view', 'create', 'edit'],
        'beyonds'       => ['view', 'create', 'edit'],
        'process-steps' => ['view', 'create', 'edit'],
        'settings'      => ['view', 'edit'],
        'messages'      => ['view'],
    ];

    public function run(): void
    {
        app()['cache']->forget(config('permission.cache.key'));

        // ── 1. Platform + content permissions → SuperAdmin ─────────────────────
        $platformNames = array_merge(
            $this->createPermissions(self::PLATFORM_PERMISSIONS),
            $this->createPermissions(self::CONTENT_PERMISSIONS),
            $this->createPermissions(self::PLAN_PERMISSIONS),
            $this->createPermissions(self::FINANCE_PERMISSIONS),
            $this->createPermissions(self::ACTIVITY_PERMISSIONS),
        );

        $superadmin = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $superadmin->syncPermissions($platformNames);

        // ── 3. Manager ────────────────────────────────────────────────────────
        $managerNames = $this->createPermissions(self::MANAGER_PERMISSIONS);
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->syncPermissions($managerNames);

        // ── 7. Default SuperAdmin user ────────────────────────────────────────
        $adminUser = User::withoutGlobalScopes()->firstOrCreate(
            ['email' => 'superadmin@iqbolshoh.uz'],
            [
                'name'     => 'Iqbolshoh',
                'password' => bcrypt('Iiqbolsho7'),
            ]
        );
        $adminUser->syncRoles(['superadmin']);

        $this->command?->info('✓ Permissions and roles seeded.');
        $this->command?->info('  superadmin  → ' . count($platformNames) . ' permissions');
        $this->command?->info('  manager     → ' . count($managerNames) . ' permissions');
        $this->command?->info('  Login: superadmin@iqbolshoh.uz / Iiqbolsho7');
    }

    private function createPermissions(array $config): array
    {
        $names = [];
        foreach ($config as $resource => $actions) {
            foreach ($actions as $action) {
                $perm    = Permission::firstOrCreate(['name' => "{$resource}.{$action}", 'guard_name' => 'web']);
                $names[] = $perm->name;
            }
        }
        return $names;
    }
}
