<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    
    private $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->newUser();

        $this->admin = $this->newAdmin();
    }

    protected function newUser()
    {
        return User::factory()->create();
    }

    protected function newAdmin()
    {
        return User::factory()->create(["is_admin" => true]);
    }

    public function test_unauthenticated_users_cannot_access_products()
    {
        $response = $this->get(route("products.index"));

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route("login"));
    }

    public function test_admin_can_see_delete_product_button()
    {
        Product::factory()->create();

        $response = $this->actingAs($this->admin)->get(route("products.index"));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee(__('Delete'));
    }

    public function test_non_admin_cannot_see_delete_product_button()
    {
        Product::factory()->create();

        $response = $this->actingAs($this->user)->get(route("products.index"));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertDontSee(__('Delete'));
    }

    public function test_admin_can_delete_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route("products.destroy", $product->id));

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route("products.index"));
    }

    public function test_user_cannot_delete_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)->delete(route("products.destroy", $product->id));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_products_list_contains_empty_table()
    {
        $response = $this->actingAs($this->user)->get(route("products.index"));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee(__('No products found !'));
    }

    public function test_products_list_contains_non_empty_table()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)->get(route("products.index"));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertDontSee(__('No products found !'));
        $response->assertViewHas("products", function ($collection) use ($product) {
            return $collection->contains($product);
        });
    }

    public function test_paginated_products_table_doesnt_contain_product_from_page_2()
    {
        $products = Product::factory(16)->create();
        $lastProduct = $products->last();

        $response = $this->actingAs($this->user)->get(route("products.index"));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertViewHas("products", function ($collection) use ($lastProduct) {
            return ! $collection->contains($lastProduct);
        });
    }
}
