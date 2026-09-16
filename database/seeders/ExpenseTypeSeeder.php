<?php

namespace Database\Seeders;

use App\Models\ExpenseType;
use Illuminate\Database\Seeder;

class ExpenseTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Accommodation', 'description' => 'Hotel or lodge stays'],
            ['name' => 'Communication', 'description' => 'Airtime or internet data'],
            ['name' => 'Fuel', 'description' => 'Vehicle fuel expenses'],
            ['name' => 'Meals', 'description' => 'Food and beverages'],
            ['name' => 'Other', 'description' => 'Miscellaneous expenses'],
            ['name' => 'Supplies', 'description' => 'Office or project supplies'],
            ['name' => 'Transport', 'description' => 'Public transport or taxi'],
        ];

        $names = collect($types)->pluck('name')->toArray();
        ExpenseType::withTrashed()->whereNotIn('name', $names)->forceDelete();

        foreach ($types as $type) {
            $existing = ExpenseType::withTrashed()->where('name', $type['name'])->first();
            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                }
                $existing->update(['description' => $type['description']]);
            } else {
                ExpenseType::create($type);
            }
        }
    }
}
