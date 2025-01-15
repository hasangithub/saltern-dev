<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(BuyerSeeder::class);
        $this->call(OwnerSeeder::class);
        $this->call(AccountGroupsTableSeeder::class);
        $this->call(ExpenseCategorySeeder::class);
        $this->call(IncomeCategoriesSeeder::class);
        $this->call(SideSeeder::class);
    }
}
