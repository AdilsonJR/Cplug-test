<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class LocalProductsSeeder extends Seeder
{
    public function run(): void
    {
        if(Product::count() > 0) {
            $this->command->info('Já existe produtos cadastrados na base de dados.');
            return;
        }

        $products = [
            [
                'sku' => 'SKU-00001',
                'name' => 'Teclado Mecanico',
                'description' => 'Teclado ABNT2 com switches silenciosos.',
                'cost_price' => 180.00,
                'sale_price' => 299.90,
            ],
            [
                'sku' => 'SKU-00002',
                'name' => 'Mouse Gamer',
                'description' => 'Mouse ergonomico com 6 botoes.',
                'cost_price' => 90.00,
                'sale_price' => 159.90,
            ],
            [
                'sku' => 'SKU-00003',
                'name' => 'Headset USB',
                'description' => 'Headset com microfone e isolamento basico.',
                'cost_price' => 120.00,
                'sale_price' => 219.90,
            ],
            [
                'sku' => 'SKU-00004',
                'name' => 'Monitor 24',
                'description' => 'Monitor Full HD com painel IPS.',
                'cost_price' => 650.00,
                'sale_price' => 999.90,
            ],
            [
                'sku' => 'SKU-00005',
                'name' => 'Hub USB',
                'description' => 'Hub USB 3.0 com 4 portas.',
                'cost_price' => 35.00,
                'sale_price' => 79.90,
            ],
        ];

        foreach ($products as $product) {
            Product::factory()->create($product);
        }
    }
}
