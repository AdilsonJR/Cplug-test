<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_request_returns_validation_errors(): void
    {
        $response = $this->postJson('/api/inventory', []);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_sales_request_returns_validation_errors(): void
    {
        $response = $this->postJson('/api/sales', []);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_sales_report_request_returns_validation_errors(): void
    {
        $response = $this->getJson('/api/reports/sales?start_date=2024-02-01&end_date=2024-01-01');

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_sales_request_requires_unit_price(): void
    {
        $product = Product::factory()->create();

        $response = $this->postJson('/api/sales', [
            'items' => [
                [
                    'sku' => $product->sku,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }
}
