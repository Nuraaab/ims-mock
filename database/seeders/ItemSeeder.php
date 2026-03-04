<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('items')->insert([
            'name' => 'Sample Item',
            'item_category_id' => null,
            'item_type' => 'product',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
