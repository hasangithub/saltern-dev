<?php

namespace Database\Seeders;

use App\Models\Side;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sides = ['east', 'west'];

        foreach ($sides as $side) {
            Side::create(['name' => $side]);
        }
    }
}
