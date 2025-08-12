<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('property_images')->insert([
            [
                'property_id' => 1,
                'image_path' => '/storage/properties/1.jpg',
                'image_name' => '1.jpg',
                'is_primary' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'property_id' => 2,
                'image_path' => '/storage/properties/2.jpg',
                'image_name' => '2.jpg',
                'is_primary' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'property_id' => 3,
                'image_path' => '/storage/properties/3.jpg',
                'image_name' => '3.jpg',
                'is_primary' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'property_id' => 4,
                'image_path' => '/storage/properties/4.jpg',
                'image_name' => '4.jpg',
                'is_primary' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'property_id' => 5,
                'image_path' => '/storage/properties/5.jpg',
                'image_name' => '5.jpg',
                'is_primary' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
