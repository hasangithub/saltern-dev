<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuyerSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('buyers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('buyers')->insert([
            [
                'business_name' => 'Sathya Enterprises',
                'business_registration_number' => '30/524',
                'full_name' => 'K S Mani',
                'credit_limit' => 100000.00,
                'service_out' => false,
                'address_1' => 'No 17 Anurathapura Road, Puttalam',
                'phone_number' => '0722434070',
                'secondary_phone_number' => '0722434070',
                'whatsapp_number' => '0722434070',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Multi Salt pvt ltd',
                'business_registration_number' => 'PV 93961',
                'full_name' => 'M S M Raheem',
                'credit_limit' => 1000000.00,
                'service_out' => false,
                'address_1' => 'Colombo Road, Rathmalyaya Puttalam',
                'phone_number' => '0772313321',
                'secondary_phone_number' => '0720573788',
                'whatsapp_number' => '0772313321',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Safrin Traders',
                'business_registration_number' => '30/1353',
                'full_name' => 'A M Safrin',
                'credit_limit' => 1000000.00,
                'service_out' => false,
                'address_1' => 'No 80C, 02nd Mail Post Mannar Road, Puttalam',
                'phone_number' => '0716120656',
                'secondary_phone_number' => '0716196267',
                'whatsapp_number' => '0710717177',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'S O Products',
                'business_registration_number' => '',
                'full_name' => 'S Omer',
                'credit_limit' => 1000000.00,
                'service_out' => false,
                'address_1' => 'No 24/2, Colombo Road, Palavi Puttalam',
                'phone_number' => '0777624387',
                'secondary_phone_number' => '0777624387',
                'whatsapp_number' => '0777624387',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Safrin Traders',
                'business_registration_number' => '30/1353',
                'full_name' => 'A M Safrin',
                'credit_limit' => 1000000.00,
                'service_out' => false,
                'address_1' => 'No 80C, 02nd Mail Post Mannar Road, Puttalam',
                'phone_number' => '0716120656',
                'secondary_phone_number' => '0716196267',
                'whatsapp_number' => '0710717177',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Prinz Salt',
                'business_registration_number' => '30/2405',
                'full_name' => 'B H P Hazri',
                'credit_limit' => 1000000.00,
                'service_out' => false,
                'address_1' => '04th Mile Post, Anuradhpura Road, Sirambiyadiya Puttalam',
                'phone_number' => '0716128553',
                'secondary_phone_number' => '0719188776',
                'whatsapp_number' => '0716128553',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Diamond Salt pvt Ltd',
                'business_registration_number' => '',
                'full_name' => 'S H M Ihthiyas',
                'credit_limit' => 1000000.00,
                'service_out' => false,
                'address_1' => 'No 83, 05th Cross Street Puttalam',
                'phone_number' => '0767059778',
                'secondary_phone_number' => '0767059778',
                'whatsapp_number' => '0767059778',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Ghouse Cottage Industries',
                'business_registration_number' => '30/454',
                'full_name' => 'M T M Ghouse',
                'credit_limit' => 1000000.00,
                'service_out' => false,
                'address_1' => 'Old Saltern Site, Mannar Road Puttalam',
                'phone_number' => '0713542333',
                'secondary_phone_number' => '0714411599',
                'whatsapp_number' => '0713542333',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
