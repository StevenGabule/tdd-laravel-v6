<?php

namespace Tests\Feature;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test*/
    public function can_get_index() : void
    {
        $product1 = $this->create('Product');
        $product2 = $this->create('Product');
        $product3 = $this->create('Product');
        $res = $this->json('GET', '/api/products');
        $res->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'slug', 'price', 'created_at']
            ],
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'last_page', 'from', 'to', 'path', 'per_page', 'total']
        ]);
        \Log::info($res->getContent());
    }

    public function testCreateProduct(): void
    {
        $faker = Factory::create();

        $res = $this->json('POST', '/api/products', [
            'name' => $name = $faker->company,
            'slug' => Str::slug($faker->name),
            'price' => $price = random_int(10, 100)
        ]);

        $res->assertJsonStructure([
            'id', 'name', 'slug', 'price', 'created_at'
        ])->assertJson([
            'name' => $name,
            'slug' => Str::slug($name),
            'price' => $price
        ])->assertStatus(201);

        $this->assertDatabaseHas('products', [
            'name' => $name,
            'slug' => Str::slug($name),
            'price' => $price
        ]);
    }

    /** @test */
    public function will_fail_with_a_404_if_product_is_not_found(): void
    {
        $res = $this->json('GET', 'api/products/-1');
        $res->assertStatus(404);
    }
    /** @test */
    public function will_fail_with_a_404_if_product_we_want_to_delete_is_null(): void
    {
        $res = $this->json('GET', 'api/products/-1');
        $res->assertStatus(404);
    }

    public function testCanReturnProduct(): void
    {
        $product = $this->create('Product');
        $res = $this->json('GET', "api/products/$product->id");
        $res->assertStatus(200)->assertExactJson([
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $product->price,
            'created_at' => $product->created_at
        ]);
    }

    /** @test */
    public function can_delete_a_product() : void
    {
        $product = $this->create('Product');
        $res = $this->json('DELETE', "api/products/$product->id");
        $res->assertStatus(204)->assertSee(null);
    }

    /** @test */
    public function can_update_product() :void
    {
        $product = $this->create('Product');
        $response =  $this->json('PUT', "api/products/$product->id", [
            'name' => $product->name . '_updated',
            'slug' => Str::slug($product->name . '_updated'),
            'price' => $product->price + 10
        ]);
        $response->assertStatus(200)->assertExactJson([
            'id' =>$product->id,
            'name' => $product->name . '_updated',
            'slug' => Str::slug($product->name.'_updated'),
            'price' => $product->price + 10,
            'created_at' => $product->created_at
        ]);
        $this->assertDatabaseHas('products', [
            'id' =>$product->id,
            'name' => $product->name . '_updated',
            'slug' => Str::slug($product->name.'_updated'),
            'price' => $product->price + 10,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ]);
    }
}
