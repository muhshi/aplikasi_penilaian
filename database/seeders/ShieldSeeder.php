<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class ShieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat semua permission dan 3 role (super_admin, ketua_tim, pegawai).
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // =====================================================
        // 1. Generate semua permission
        // =====================================================

        // Resource permissions (format: Action:ModelName)
        $resourcePermissions = $this->generateResourcePermissions();

        // Page permissions
        $pagePermissions = [
            'View:MonitoringCkpKipapp',
        ];

        // Widget permissions
        $widgetPermissions = [
            'View:StatsOverview',
            'View:TopPegawai',
            'View:MonitoringPending',
            'View:NilaiDistributionChart',
            'View:NilaiPegawaiRekapWidget',
        ];

        $allPermissions = array_merge($resourcePermissions, $pagePermissions, $widgetPermissions);

        // Buat semua permission
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // =====================================================
        // 2. Buat roles dan assign permissions
        // =====================================================

        // --- Super Admin: semua permission ---
        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);
        $superAdmin->syncPermissions($allPermissions);

        // --- Ketua Tim ---
        $ketuaTimPermissions = array_merge(
            // CkpKipapp: view
            $this->permissionsFor('CkpKipapp', ['ViewAny', 'View']),
            // NilaiKipapp: view
            $this->permissionsFor('NilaiKipapp', ['ViewAny', 'View']),
            // NilaiPegawai: full CRUD
            $this->permissionsFor('NilaiPegawai', ['ViewAny', 'View', 'Create', 'Update', 'Delete']),
            // Pegawai: view
            $this->permissionsFor('Pegawai', ['ViewAny', 'View']),
            // Pages
            ['View:MonitoringCkpKipapp'],
            // Widgets
            [
                'View:StatsOverview',
                'View:TopPegawai',
                'View:MonitoringPending',
                'View:NilaiDistributionChart',
                'View:NilaiPegawaiRekapWidget',
            ],
        );

        $ketuaTim = Role::firstOrCreate([
            'name' => 'ketua_tim',
            'guard_name' => 'web',
        ]);
        $ketuaTim->syncPermissions($ketuaTimPermissions);

        // --- Pegawai ---
        $pegawaiPermissions = array_merge(
            // CkpKipapp: view + create + update (upload CKP sendiri)
            $this->permissionsFor('CkpKipapp', ['ViewAny', 'View', 'Create', 'Update']),
            // NilaiKipapp: view only
            $this->permissionsFor('NilaiKipapp', ['ViewAny', 'View']),
            // NilaiPegawai: view only (Agar menu Nilai Pegawai muncul)
            $this->permissionsFor('NilaiPegawai', ['ViewAny', 'View']),
            // Widgets (tanpa MonitoringPending)
            [
                'View:StatsOverview',
                'View:TopPegawai',
                'View:NilaiDistributionChart',
            ],
        );

        $pegawai = Role::firstOrCreate([
            'name' => 'pegawai',
            'guard_name' => 'web',
        ]);
        $pegawai->syncPermissions($pegawaiPermissions);

        // =====================================================
        // 3. Assign super_admin role ke user admin
        // =====================================================
        $admin = User::where('email', 'admin@gmail.com')->first();
        if ($admin && !$admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
        }

        $this->command->info('✅ Shield roles & permissions seeded successfully!');
        $this->command->table(
            ['Role', 'Permissions Count'],
            [
                ['super_admin', $superAdmin->permissions()->count()],
                ['ketua_tim', $ketuaTim->permissions()->count()],
                ['pegawai', $pegawai->permissions()->count()],
            ]
        );
    }

    /**
     * Generate resource permissions untuk semua model.
     */
    private function generateResourcePermissions(): array
    {
        $models = [
            'CkpKipapp',
            'NilaiKipapp',
            'NilaiPegawai',
            'Pegawai',
            'PeriodeTahun',
            'User',
        ];

        $actions = [
            'ViewAny',
            'View',
            'Create',
            'Update',
            'Delete',
            'Restore',
            'ForceDelete',
            'ForceDeleteAny',
            'RestoreAny',
            'Replicate',
            'Reorder',
        ];

        $permissions = [];
        foreach ($models as $model) {
            foreach ($actions as $action) {
                $permissions[] = "{$action}:{$model}";
            }
        }

        // Role resource permissions (Filament Shield)
        $roleActions = ['ViewAny', 'View', 'Create', 'Update', 'Delete'];
        foreach ($roleActions as $action) {
            $permissions[] = "{$action}:Role";
        }

        return $permissions;
    }

    /**
     * Helper: generate permission names untuk model tertentu.
     */
    private function permissionsFor(string $model, array $actions): array
    {
        return array_map(fn(string $action) => "{$action}:{$model}", $actions);
    }
}
