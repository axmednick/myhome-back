<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $packages = [
            // Agentliklər üçün paketlər
            [
                'name' => 'Basic Agency',
                'price' => 95,
                'listing_limit' => 100,
                'bonus' => 50,
                'type' => 'agency'
            ],
            [
                'name' => 'Standard Agency',
                'price' => 190,
                'listing_limit' => 150,
                'bonus' => 100,
                'type' => 'agency'
            ],
            [
                'name' => 'Premium Agency',
                'price' => 390,
                'listing_limit' => 400,
                'bonus' => 250,
                'type' => 'agency'
            ],

            // Fiziki şəxs rieltorlar üçün paketlər
            [
                'name' => 'Basic Realtor',
                'price' => 30,
                'listing_limit' => 20,
                'bonus' => 10,
                'type' => 'individual'
            ],
            [
                'name' => 'Standard Realtor',
                'price' => 60,
                'listing_limit' => 50,
                'bonus' => 25,
                'type' => 'individual'
            ],
            [
                'name' => 'Premium Realtor',
                'price' => 120,
                'listing_limit' => 120,
                'bonus' => 50,
                'type' => 'individual'
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}
