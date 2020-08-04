<?php

namespace Tests\Feature;

use App\Category;
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
class ApiCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $endpoint = 'api/categories';

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function paginate_function()
    {
        $this->withoutExceptionHandling();
        factory(Category::class, 20)->create();

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
        factory(Category::class, 1)->create();
        $model = Category::first();

        $response = $this->getJson($this->endpoint . '/' . $model->id);
        $response->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => $this->fields,
                ]
            );
    }

    /** @test */
    public function store_function()
    {
        $this->withoutExceptionHandling();
        $this->actingAs(factory(User::class)->create(), 'api');
        $this->assertAuthenticated();

        $response = $this->postJson($this->endpoint, $this->data());
        $this->assertCount(1, Category::all());
        $response->assertStatus(201)
            ->assertJsonStructure(
                [
                    'data' => $this->fields,
                ]
            );
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
        $this->assertCount(0, Category::all());

        $response = $this->postJson($this->endpoint, array_merge($this->data(), ['parent_id' => 'asda']));
        $response->assertStatus(422); // message: The given data was invalid.
        $response->assertJson([
            "message" => "The given data was invalid.",
            "errors" => [
                'parent_id' => ["The parent id must be an integer."]
            ]
        ]);
        $this->assertCount(0, Category::all());
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
                    'data' => $this->fields,
                ]
            );
        $this->assertCount(1, Category::all());
        $model = Category::first();

        $response = $this->putJson($this->endpoint . "/{$model->id}", array_merge($this->data(), [
            'name' => 'Puma Shoes',
            'description' => 'Puma Description'
        ]));

        $response->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => $this->fields,
                ]
            );
    }

    /** @test */
    public function delete_function()
    {
        $this->actingAs(factory(User::class)->create(), 'api');
        $this->assertAuthenticated();

        // create new category
        $response = $this->postJson($this->endpoint, $this->data());
        $response->assertStatus(201)
            ->assertJsonStructure(
                [
                    'data' => $this->fields,
                ]
            );

        $this->assertCount(1, Category::all());
        $model = Category::first();

        // create new product
        $response = $this->postJson('api/products', [
            'name' => 'Nike Shoes',
            'description' => 'Nike Description',
            'stock' => 2,
            'category_id' => $model->id
        ]);
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

        // delete that category with products
        $response = $this->deleteJson($this->endpoint . "/{$model->id}");
        $this->assertCount(0, Category::all());
        $this->assertCount(0, Product::all());
        $response->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => $this->fields,
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
                    'data' => $this->fields,
                ]
            );

        $this->assertCount(1, Category::all());

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
                    'data' => $this->fields,
                ]
            );

        $this->assertCount(1, Category::all());

        $response = $this->putJson($this->endpoint . '/2', array_merge($this->data(), [
            'name' => 'Puma Shoes',
        ]));
        $response->assertStatus(404);
    }

    /** @test */
    public function paginate_request_rule_function()
    {
        factory(Category::class, 4)->create();

        $response = $this->getJson($this->endpoint . '?filter[parent_id]=2');
        $response->assertStatus(200)
            ->assertJsonStructure(
                $this->jsonStructure()
            );

        $response = $this->getJson($this->endpoint . '?filter[parent_id]=asalaja');
        $response->assertStatus(422);
    }

    private $fields = [
        'id',
        'parent_id',
        'created_at'
    ];

    private function jsonStructure()
    {
        return [
            'data' => [
                '*' => $this->fields
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

    private function data()
    {
        return [
            'name' => 'Nike Shoes',
            'parent_id' => null
        ];
    }
}
