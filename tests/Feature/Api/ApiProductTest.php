<?php

namespace Tests\Feature\Api;

use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\User;
use App\Services\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ApiProductTest extends TestCase
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

    public function test_product_list_api_response() 
    {
        $products = $this->newProduct(10);

        $response = $this->actingAs($this->user)->getJson(route("api.products.index"));

        $products = $this->formatResourceToArray(ProductCollection::class, $products);

        $response->assertJson($products);
    }

    public function test_show_product_api_response()
    {
        $product = $this->newProduct();

        $response = $this->actingAs($this->user)->getJson(route("api.products.show", $product));

        $product = $this->formatResourceToArray(ProductResource::class, $product);
        $response->assertJson(["data" => $product]);
    }
    
    public function test_store_product_api_response()
    {
        $product = [
            "name" => $this->faker->text(30),
            "price" => $this->faker->randomFloat(2, 100, 999)
        ];

        $response = $this->actingAs($this->user)->postJson(route("api.products.store"), $product);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson(["data" => [
            "name" => $product["name"],
            "price_usd" => $product["price"],
            "price_eur" => (new CurrencyService)->convert($product["price"], "usd", "eur"),
        ]]);
    }

    public function test_invalid_store_product_api_response()
    {
        $product = [
            "name" => "",
            "price" => $this->faker->randomFloat(2, 100, 999)
        ];

        $response = $this->actingAs($this->user)->postJson(route("api.products.store"), $product);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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

    protected function formatResourceToArray($class, $resource)
    {
        return json_decode((new $class($resource))->toJson(), true);
    }
}
