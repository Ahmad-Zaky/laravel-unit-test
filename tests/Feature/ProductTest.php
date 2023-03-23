<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_list_contains_empty_table()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route("products.index"));

        $response->assertStatus(200);
        $response->assertSee(__('No products found !'));
    }

    public function test_products_list_contains_non_empty_table()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->get(route("products.index"));

        $response->assertStatus(200);
        $response->assertDontSee(__('No products found !'));
        $response->assertViewHas("products", function ($collection) use ($product) {
            return $collection->contains($product);
        });
    }
}
