<?php

namespace Database\Seeders;

use App\Models\IncomeCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IncomeCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Donations', 'description' => 'Funds received as donations'],
            ['name' => 'Grants', 'description' => 'Government or institutional grants'],
            ['name' => 'Sponsorships', 'description' => 'Sponsorship income from events or activities'],
            ['name' => 'Miscellaneous', 'description' => 'Other miscellaneous income sources'],
        ];

        foreach ($categories as $category) {
            IncomeCategory::create($category);
        }
    }
}
