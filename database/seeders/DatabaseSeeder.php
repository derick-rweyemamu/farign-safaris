<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // ── Core / Auth ──────────────────────────────────────
            RoleAndPermissionSeeder::class,

            // ── Geography ────────────────────────────────────────
            CountrySeeder::class,
            CitySeeder::class,

            // ── Finance & Currency ────────────────────────────────
            CurrencySeeder::class,
            ExpenseTypeSeeder::class,


            // ── Products ──────────────────────────────────────────
            ProductCategorySeeder::class,

            // ── Safari / Travel ───────────────────────────────────
            AnimalIconSeeder::class,

            // ── CMS / Frontend ────────────────────────────────────
            NavigationMenuSeeder::class,
        ]);
    }
}