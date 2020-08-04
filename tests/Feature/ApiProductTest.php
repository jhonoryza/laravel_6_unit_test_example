<?php

namespace Tests\Feature;

use App\Product;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 401 unauthenticated
 * 403 unauthorized
 * 404 not found
 * 201 created
 * 200 get, update, delete
 * 422 data invalid or validation error
 */
class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected $endpoint = 'api/products';

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function paginate_function()
    {
        $this->withoutExceptionHandling();
        factory(Product::class, 20)->create();

        $response = $this->getJson($this->endpoint);
        $response->assertStatus(200)
            ->assertJsonStructure(
                $this->jsonStructure()
            );
    }

    /** @test */
    public function show_function()
    {
        $this->withoutExceptionHandling();
        factory(Product::class, 1)->create();
        $product = Product::first();

        $response = $this->getJson($this->endpoint . '/' . $product->id);
        $response->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'name',
                        'description',
                        'stock',
                        'category_id',
                        'created_at'
                    ],
                ]
            );
    }

    private function jsonStructure()
    {
        return [
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'stock',
                    'category_id',
                    'created_at'
                ]
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next'
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'path',
                'per_page',
                'to',
                'total'
            ]
        ];
    }


    /** @test */
    public function store_function()
    {
        $this->withoutExceptionHandling();
        $this->actingAs(factory(User::class)->create(), 'api');
        $this->assertAuthenticated();

        $response = $this->postJson($this->endpoint, $this->data());
        $this->assertCount(1, Product::all());
        $response->assertStatus(201)
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'name',
                        'description',
                        'stock',
                        'category_id',
                        'created_at'
                    ],
                ]
            );
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
        $this->actingAs(factory(User::class)->create(), 'api');
        $this->assertAuthenticated();

        $response = $this->postJson($this->endpoint, array_merge($this->data(), ['name' => '']));
        $response->assertStatus(422); // message: The given data was invalid.
        $response->assertJson([
            "message" => "The given data was invalid.",
            "errors" => [
                'name' => ["The name field is required."]
            ]
        ]);
        $this->assertCount(0, Product::all());

        $response = $this->postJson($this->endpoint, array_merge($this->data(), ['stock' => '']));
        $response->assertStatus(422); // message: The given data was invalid.
        $response->assertJson([
            "message" => "The given data was invalid.",
            "errors" => [
                'stock' => ["The stock field is required."]
            ]
        ]);
        $this->assertCount(0, Product::all());

        $response = $this->postJson($this->endpoint, array_merge($this->data(), ['category_id' => '']));
        $response->assertStatus(422); // message: The given data was invalid.
        $response->assertJson([
            "message" => "The given data was invalid.",
            "errors" => [
                'category_id' => ["The category id field is required."]
            ]
        ]);
        $this->assertCount(0, Product::all());

        $response = $this->postJson($this->endpoint, array_merge($this->data(), ['description' => '']));
        $this->assertCount(1, Product::all());
        $response->assertStatus(201)
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'name',
                        'description',
                        'stock',
                        'category_id',
                        'created_at'
                    ],
                ]
            );
    }

    /** @test */
    public function update_function()
    {
        $this->actingAs(factory(User::class)->create(), 'api');
        $this->assertAuthenticated();
        $response = $this->postJson($this->endpoint, $this->data());

        $response->assertStatus(201)
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'name',
                        'description',
                        'stock',
                        'category_id',
                        'created_at'
                    ],
                ]
            );
        $this->assertCount(1, Product::all());
        $product = Product::first();

        $response = $this->putJson($this->endpoint . "/{$product->id}", array_merge($this->data(), [
            'name' => 'Puma Shoes',
            'description' => 'Puma Description'
        ]));

        $response->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'name',
                        'description',
                        'stock',
                        'category_id',
                        'created_at'
                    ],
                ]
            );
    }

    /** @test */
    public function delete_function()
    {
        $this->actingAs(factory(User::class)->create(), 'api');
        $this->assertAuthenticated();

        $response = $this->postJson($this->endpoint, $this->data());
        $response->assertStatus(201)
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'name',
                        'description',
                        'stock',
                        'category_id',
                        'created_at'
                    ],
                ]
            );

        $this->assertCount(1, Product::all());
        $product = Product::first();

        $response = $this->deleteJson($this->endpoint . "/{$product->id}");
        $this->assertCount(0, Product::all());
        $response->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'name',
                        'description',
                        'stock',
                        'category_id',
                        'created_at'
                    ],
                ]
            );
    }

    /** @test */
    public function delete_not_exist_product_function()
    {
        $this->actingAs(factory(User::class)->create(), 'api');
        $this->assertAuthenticated();

        $response = $this->postJson($this->endpoint, $this->data());
        $response->assertStatus(201)
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'name',
                        'description',
                        'stock',
                        'category_id',
                        'created_at'
                    ],
                ]
            );

        $this->assertCount(1, Product::all());

        $response = $this->deleteJson($this->endpoint . '/2');
        $response->assertStatus(404);
    }

    /** @test */
    public function update_not_exist_product_function()
    {
        $this->actingAs(factory(User::class)->create(), 'api');
        $this->assertAuthenticated();

        $response = $this->postJson($this->endpoint, $this->data());
        $response->assertStatus(201)
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'name',
                        'description',
                        'stock',
                        'category_id',
                        'created_at'
                    ],
                ]
            );

        $this->assertCount(1, Product::all());

        $response = $this->putJson($this->endpoint . '/2', array_merge($this->data(), [
            'name' => 'Puma Shoes',
            'description' => 'Puma Description'
        ]));
        $response->assertStatus(404);
    }

    /** @test */
    public function paginate_request_rule_function()
    {
        factory(Product::class, 4)->create();

        $response = $this->getJson($this->endpoint . '?filter[stock]=2');
        $response->assertStatus(200)
            ->assertJsonStructure(
                $this->jsonStructure()
            );

        $response = $this->getJson($this->endpoint . '?filter[stock]=asalaja');
        $response->assertStatus(422);
    }
}
