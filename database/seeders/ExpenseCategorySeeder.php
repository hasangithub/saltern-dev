<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExpenseCategory::create([
            'name' => 'Office Supplies',
        ]);

        ExpenseCategory::create([
            'name' => 'Travel',
        ]);

        ExpenseCategory::create([
            'name' => 'Utilities',
        ]);

        ExpenseCategory::create([
            'name' => 'Salaries',
        ]);

        ExpenseCategory::create([
            'name' => 'Maintenance',
        ]);
    }
}
