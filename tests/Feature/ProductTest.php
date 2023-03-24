<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $user;
    
    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->setUpFaker();

        $this->user = $this->newUser();

        $this->admin = $this->newAdmin();
    }

    public function test_unauthenticated_users_cannot_access_products()
    {
        $response = $this->get(route("products.index"));

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route("login"));
    }

    public function test_admin_can_see_delete_product_button()
    {
        $this->newProduct();

        $response = $this->actingAs($this->admin)->get(route("products.index"));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee(__('Delete'));
    }

    public function test_non_admin_cannot_see_delete_product_button()
    {
        $this->newProduct();

        $response = $this->actingAs($this->user)->get(route("products.index"));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertDontSee(__('Delete'));
    }

    public function test_admin_can_delete_product()
    {
        $response = $this->actingAs($this->admin)->delete(route("products.destroy", $this->newProduct()->id));

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route("products.index"));
    }

    public function test_user_cannot_delete_product()
    {
        $response = $this->actingAs($this->user)->delete(route("products.destroy", $this->newProduct()->id));

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
        $product = $this->newProduct();

        $response = $this->actingAs($this->user)->get(route("products.index"));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertDontSee(__('No products found !'));
        $response->assertViewHas("products", function ($collection) use ($product) {
            return $collection->contains($product);
        });
    }

    public function test_paginated_products_table_doesnt_contain_product_from_page_2()
    {
        $products = $this->newProduct(16);
        $lastProduct = $products->last();

        $response = $this->actingAs($this->user)->get(route("products.index"));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertViewHas("products", function ($collection) use ($lastProduct) {
            return ! $collection->contains($lastProduct);
        });
    }

    public function test_create_product_successful()
    {
        $product = [
            "name" => $this->faker->text(30),
            "price" => $this->faker->randomFloat(2, 100, 999)
        ];

        $response = $this->actingAs($this->user)->post(route("products.store"), $product);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route("products.index"));
        $this->assertDatabaseHas((new Product)->getTable(), $product);

        $lastProduct = $this->lastProduct();

        $this->assertEquals($product["name"], $lastProduct->name);
        $this->assertEquals($product["price"], $lastProduct->price);
    }

    public function test_edit_product_with_correct_values()
    {
        $product = $this->newProduct();
        $response = $this->actingAs($this->user)->get(route("products.edit", $product));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee('value="'. $product->name  .'"', false);
        $response->assertSee('value="'. $product->price  .'"', false);
        $response->assertViewHas('product', $product);
    }

    public function test_update_product_validation_redirects_back_on_any_invalids()
    {
        $response = $this->actingAs($this->user)->put(route("products.update", $this->newProduct()), [
            "name" => "",
            "price" => $this->faker->randomFloat(2, 100, 999),
        ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasErrors(["name"]);
        $response->assertInvalid(["name"]); // are same as the above assertion.

    }

    public function test_delete_product_successful()
    {
        $product = $this->newProduct();

        $response = $this->actingAs($this->admin)->delete(route("products.destroy", $product));

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route("products.index"));
        $this->assertDatabaseMissing((new Product)->getTable(), $product->toArray());
        $this->assertDatabaseCount((new Product)->getTable(), 0);
    }

    protected function newUser($number = NULL)
    {
        return $number
            ? User::factory($number)->create()
            : User::factory()->create();
    }

    protected function newAdmin($number = NULL)
    {
        return $number
            ? User::factory($number)->create(["is_admin" => true])
            : User::factory()->create(["is_admin" => true]);
    }

    protected function newProduct($number = NULL)
    {
        return $number
            ? Product::factory($number)->create()
            : Product::factory()->create();
    }

    protected function lastProduct()
    {
        return Product::orderBy("id", "DESC")->first();
    }
}
