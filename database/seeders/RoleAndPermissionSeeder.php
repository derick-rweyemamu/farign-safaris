<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Reset cached roles and permissions ───────────────────────────
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $systemPermissions = [
            'view dashboard',
            'view client area',       // Client portal access
            'view profile',           // My Profile (settings.profile)
            'view settings',          // General settings menu guard
            'view company settings',  // Company & email settings
            'edit general settings',
            'edit invoices settings',
            'edit estimates settings',
            'edit bank details',
            'enable disable 2FA',
            'view currencies setting',
            'add currency details',
            'edit currency details',
            'delete currency details',
            'view banks',
            'create banks',
            'edit banks',
            'delete banks',
        ];

        // ─── Estimate Permissions ──────────────────────────────────────────
        $estimatePermissions = [
            'view estimates',
            'create estimate',
            'edit estimate',
            'print estimate',
            'send estimate',
            'delete estimate',
            'convert to invoice',
            'accept estimate',
            'reject estimate',
            'restore estimates',
            'force delete estimates',
            'view estimate reports',
        ];

        // ─── Invoice Permissions ───────────────────────────────────────────
        $invoicePermissions = [
            'view invoices',
            'create invoice',
            'edit invoice',
            'print invoice',
            'send invoice',
            'delete invoice',
            'restore invoices',
            'force delete invoices',
            'view invoice reports',
        ];

        // ─── Client Permissions ────────────────────────────────────────────
        $clientPermissions = [
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',
            'view client details',
            'view client reports',
            'export clients',
            'view newsletters',
            'import clients',
            'print clients',
            'restore clients',
            'force delete clients',
            'send email',
            'change client password',
            'reset client password',
        ];

        // ─── Payment Permissions ───────────────────────────────────────────
        $paymentPermissions = [
            'view payments',
            'create payment',
            'edit payment',
            'delete payment',
            'print payment',
        ];

        // ─── Expense Permissions ───────────────────────────────────────────
        $expensePermissions = [
            'view expenses',
            'create expense',
            'edit expense',
            'delete expense',
            'view expense reports',
            'view profit loss',
        ];

        // ─── Invoice Expense Permissions ───────────────────────────────────
        $invoiceExpensePermissions = [
            'view invoice expenses',
            'create invoice expense',
            'edit invoice expense',
            'delete invoice expense',
            'print invoice expense',
        ];

        // ─── Expense Type Permissions ──────────────────────────────────────
        $expenseTypePermissions = [
            'view expense type',
            'create expense type',
            'edit expense type',
            'delete expense type',
        ];

        // ─── CRM / Lead Permissions ────────────────────────────────────────
        $crmLeadPermissions = [
            'view leads',
            'create leads',
            'edit leads',
            'delete leads',
            'change lead status',
            'log lead activities',
            'delete lead activities',
            'view newsletters',
            'create newsletters',
            'edit newsletters',
            'delete newsletters',
        ];

        // ─── Product Permissions ───────────────────────────────────────────
        $productPermissions = [
            'view product',
            'create product',
            'edit product',
            'delete product',
            'restore product',
            'view product reports',
            'view product category',
            'create product category',
            'edit product category',
            'delete product category',
            'restore product category',
            'force delete product category',
        ];

        // ─── Itinerary & Tour Resource Permissions ─────────────────────────
        $itineraryPermissions = [
            'view itineraries',
            'create itineraries',
            'edit itineraries',
            'delete itineraries',

            'view destinations',
            'create destinations',
            'edit destinations',
            'delete destinations',

            'view accommodations',
            'create accommodations',
            'edit accommodations',
            'delete accommodations',

            'view tour activities',
            'create tour activities',
            'edit tour activities',
            'delete tour activities',

            'view transports',
            'create transports',
            'edit transports',
            'delete transports',

            'view meal plans',
            'create meal plans',
            'edit meal plans',
            'delete meal plans',

            'view tour categories',
            'create tour categories',
            'edit tour categories',
            'delete tour categories',
            'restore tour categories',
            'force delete tour categories',

            // Cost Sheets
            'view cost sheets',
            'create cost sheets',
            'edit cost sheets',
            'delete cost sheets',
            'finalize cost sheets',
        ];

        // ─── CMS Permissions ───────────────────────────────────────────────
        $cmsPermissions = [
            'view pages',
            'create pages',
            'edit pages',
            'delete pages',
            'restore pages',
            'publish pages',
            'view posts',
            'create posts',
            'edit posts',
            'delete posts',
            'restore posts',
            'publish posts',
        ];

        // ─── Role & Permission Management ──────────────────────────────────
        $rolePermissions = [
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
        ];

        // ─── Activity Log ──────────────────────────────────────────────────
        $activityLogPermissions = [
            'view activities',
            'delete activities',
            'print activities',
        ];

        // ─── Location Permissions ──────────────────────────────────────────
        $locationPermissions = [
            'view countries',
            'create countries',
            'edit countries',
            'delete countries',
            'view cities',
            'create cities',
            'edit cities',
            'delete cities',
        ];


        // ─── Create all permissions ────────────────────────────────────────
        $allGroups = [
            $systemPermissions,
            $estimatePermissions,
            $invoicePermissions,
            $clientPermissions,
            $paymentPermissions,
            $expensePermissions,
            $invoiceExpensePermissions,
            $expenseTypePermissions,
            $crmLeadPermissions,
            $productPermissions,
            $itineraryPermissions,
            $cmsPermissions,
            $rolePermissions,
            $activityLogPermissions,
            $locationPermissions,
        ];

        foreach ($allGroups as $group) {
            foreach ($group as $perm) {
                Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            }
        }

        // ─── Roles ─────────────────────────────────────────────────────────

        // Super Admin — full access
        $superadmin = Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
        $superadmin->syncPermissions(Permission::all());

        // Admin — full access
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // Manager — broad operational access
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'view dashboard',
            'view profile',

            // Estimates
            'view estimates',
            'create estimate',
            'edit estimate',
            'print estimate',
            'send estimate',
            'delete estimate',
            'convert to invoice',
            'accept estimate',
            'reject estimate',
            'view estimate reports',

            // Invoices
            'view invoices',
            'create invoice',
            'edit invoice',
            'print invoice',
            'send invoice',
            'delete invoice',
            'view invoice reports',

            // Clients
            'view clients',
            'create clients',
            'edit clients',
            'view client details',
            'view client reports',
            'send email',
            'reset client password',

            // Payments
            'view payments',
            'create payment',
            'edit payment',
            'print payment',

            // Expenses
            'view expenses',
            'create expense',
            'edit expense',
            'view expense reports',
            'view profit loss',
            'view invoice expenses',
            'view expense type',

            // CRM
            'view leads',
            'create leads',
            'edit leads',
            'change lead status',
            'log lead activities',

            // Products & Tours
            'view product',
            'view product category',
            'view itineraries',
            'view destinations',
            'view accommodations',
            'view tour activities',
            'view transports',
            'view meal plans',
            'view tour categories',

            // Cost Sheets
            'view cost sheets',
            'create cost sheets',
            'edit cost sheets',
            'delete cost sheets',
            'finalize cost sheets',

            // Reports
            'view activities',
        ]);

        // Editor — CMS focused
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editor->syncPermissions([
            'view dashboard',
            'view profile',
            'view pages',
            'create pages',
            'edit pages',
            'delete pages',
            'publish pages',
            'view posts',
            'create posts',
            'edit posts',
            'delete posts',
            'publish posts',
            'view tour categories',
            'create tour categories',
            'edit tour categories',
            'view product',
            'view itineraries',
            'view destinations',
            'view accommodations',
            'view tour activities',
            'view transports',
            'view meal plans',
        ]);

        // Staff — sales/operations
        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staff->syncPermissions([
            'view dashboard',
            'view profile',

            // Clients
            'view clients',
            'view client details',

            // Estimates
            'view estimates',
            'create estimate',
            'edit estimate',
            'print estimate',
            'send estimate',

            // Invoices
            'view invoices',
            'print invoice',

            // Payments
            'view payments',

            // CRM Leads
            'view leads',
            'create leads',
            'edit leads',
            'change lead status',
            'log lead activities',

            // Products & Tours
            'view product',
            'view itineraries',
            'view destinations',
            'view accommodations',
            'view tour activities',
            'view transports',
            'view meal plans',
            'view tour categories',

            // Cost Sheets
            'view cost sheets',
            'create cost sheets',
            'edit cost sheets',
        ]);

        // Tour Guide — minimal
        $tourguide = Role::firstOrCreate(['name' => 'tourguide', 'guard_name' => 'web']);
        $tourguide->syncPermissions([
            'view dashboard',
            'view profile',
            'view itineraries',
            'view destinations',
            'view accommodations',
            'view tour activities',
            'view transports',
            'view meal plans',
        ]);

        // Client — portal only
        $client = Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);
        $client->syncPermissions([
            'view client area',
            'view profile',
        ]);

        // ─── Default Admin Users ───────────────────────────────────────────
        $adminUser = User::firstOrCreate(
            ['email' => 'rweyemamuderick@gmail.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('kabyemela2020'),
            ]
        );
        $adminUser->assignRole('super admin');
        $adminUser->profile()->updateOrCreate([], [
            'status' => 'active',
            'last_login_at' => now(),
        ]);
    }
}