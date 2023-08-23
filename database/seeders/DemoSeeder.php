<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::insert([
            [
                'name' => 'C One',
                'mobile' => '01712000000',
                'address' => 'Mirpur - 1',
                'shop_name' => 'Shop One',
                'previous_due' => 0,
                'status' => 'Active',
                'created_at' => now(),
            ],
            [
                'name' => 'C Two',
                'mobile' => '01712111111',
                'address' => 'Mirpur - 2',
                'shop_name' => 'Shop Two',
                'previous_due' => 0,
                'status' => 'Active',
                'created_at' => now(),
            ]
        ]);
        
        Supplier::insert([
            [
                'name' => 'S One',
                'mobile' => '01812000000',
                'address' => 'Nikunjo - 1',
                'shop_name' => 'S Shop One',
                'previous_due' => 0,
                'status' => 'Active',
                'created_at' => now(),
            ],
            [
                'name' => 'S Two',
                'mobile' => '01812111111',
                'address' => 'Nikunjo - 2',
                'shop_name' => 'S Shop Two',
                'previous_due' => 0,
                'status' => 'Active',
                'created_at' => now(),
            ]
        ]);

        Unit::insert([
            [
                'base_unit' => 'KG',
                'name' => '25KG',
                'quantity' => 25,
                'status' => 'Active',
                'created_at' => now(),
            ],
            [
                'base_unit' => 'Litre',
                'name' => '5Litre',
                'quantity' => 5,
                'status' => 'Active',
                'created_at' => now(),
            ]
        ]);

        Product::insert([
            [
                'base_unit' => 'KG',
                'name' => 'Rice',
                'status' => 'Active',
                'created_at' => now(),
            ],
            [
                'base_unit' => 'Litre',
                'name' => 'Oil',
                'status' => 'Active',
                'created_at' => now(),
            ]
        ]);

       

        Bank::insert([
            [
                'name' => 'A',
                'branch' => 'A',
                'account_number' => 'A',
                'status' => 'Active',
                'created_at' => now(),
            ],
            [
                'name' => 'B',
                'branch' => 'B',
                'account_number' => 'B',
                'status' => 'Active',
                'created_at' => now(),
            ]
        ]);
    }
}
