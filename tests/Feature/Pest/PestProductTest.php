<?php

namespace Tests\Feature\Pest;

use function Pest\Faker\faker;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

uses(\Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function() {
    $this->user = newUser();
    $this->admin = newAdmin();
});

it('unauthenticated user cannot access products', function () {
    $this->get(route("products.index"))
        ->assertRedirect(route("login"));
});

it('products list contains empty table', function () {
    $this->actingAs($this->user)
        ->get(route("products.index"))
        ->assertStatus(Response::HTTP_OK)
        ->assertSee(__('No products found !'));
});

it('products list contains non empty table', function () {
    $product = newProduct();

    $this->actingAs($this->user)
        ->get(route("products.index"))
        ->assertStatus(Response::HTTP_OK)
        ->assertDontSee(__('No products found !'))
        ->assertSee($product->name)
        ->assertViewHas("products", function ($collection) use ($product) {
            return $collection->contains($product);
        });
});

it("create product successful", function () {
    $product = [
        "name" => faker()->text(30),
        "price" => faker()->randomFloat(2, 100, 999)
    ];
    
    $this->actingAs($this->user)->post(route("products.store"), $product)
        ->assertStatus(Response::HTTP_FOUND)
        ->assertRedirect(route("products.index"));

    $this->assertDatabaseHas((new Product)->getTable(), $product);
    
    $lastProduct = lastProduct();

    expect($lastProduct->name)->toBe($product["name"]);
    expect($lastProduct->price)->toBe($product["price"]);
});


