<?php

namespace Database\Seeders;
use App\Models\Order;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::create([
            'product_name' => 'Test Product',
            'quantity' => 1,
            'price' => 100,
            'status' => 'pending',
            'user_id' => 3,
        ]);
    }
}
