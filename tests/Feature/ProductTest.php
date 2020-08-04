<?php

namespace Tests\Feature;

use App\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function create_function()
    {
        $this->withoutExceptionHandling();
        $response = $this->post('products', $this->data());
        $response->assertRedirect('products');
        $this->assertCount(1, Product::all());
    }

    private function data()
    {
        return [
            'name' => 'Nike Shoes',
            'description' => 'Nike Description',
            'stock' => 2,
            'category_id' => 1
        ];
    }

    /** @test */
    public function validation_function()
    {
        $response = $this->post('products', array_merge($this->data(), ['name' => '']));
        $response->assertSessionHasErrors('name');
        $this->assertCount(0, Product::all());

        $response = $this->post('products', array_merge($this->data(), ['stock' => '']));
        $response->assertSessionHasErrors('stock');
        $this->assertCount(0, Product::all());

        $response = $this->post('products', array_merge($this->data(), ['category_id' => '']));
        $response->assertSessionHasErrors('category_id');
        $this->assertCount(0, Product::all());

        $response = $this->post('products', array_merge($this->data(), ['description' => '']));
        $this->assertCount(1, Product::all());
        $response->assertRedirect('products');
    }

    /** @test */
    public function update_function()
    {
        $response = $this->post('products', $this->data());
        $response->assertRedirect('products');
        $this->assertCount(1, Product::all());
        $product = Product::first();

        $response = $this->put('products/' . $product->id, array_merge($this->data(), [
            'name' => 'Puma Shoes',
            'description' => 'Puma Description'
        ]));
        $this->assertEquals('Puma Shoes', Product::first()->name);
        $this->assertEquals('Puma Description', Product::first()->description);
        $response->assertRedirect('products');
    }

    /** @test */
    public function delete_function()
    {
        $response = $this->post('products', $this->data());
        $response->assertRedirect('products');
        $this->assertCount(1, Product::all());
        $product = Product::first();

        $response = $this->delete('products/' . $product->id);
        $this->assertCount(0, Product::all());
        $response->assertRedirect('products');
    }

    /** @test */
    public function delete_not_exist_product_function()
    {
        $response = $this->post('products', $this->data());
        $response->assertRedirect('products');
        $this->assertCount(1, Product::all());

        $response = $this->delete('products/2');
        $response->assertStatus(404);
    }

    /** @test */
    public function update_not_exist_product_function()
    {
        $response = $this->post('products', $this->data());
        $response->assertRedirect('products');
        $this->assertCount(1, Product::all());

        $response = $this->put('products/2', array_merge($this->data(), [
            'name' => 'Puma Shoes',
            'description' => 'Puma Description'
        ]));
        $response->assertStatus(404);
    }
}
