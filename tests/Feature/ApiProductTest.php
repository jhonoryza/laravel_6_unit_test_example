<?php

namespace Tests\Feature;

use App\Product;
use App\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Passport\Passport;

/**
 * 401 unauthenticated
 * 403 unauthorized
 * 404 not found
 * 201 created
 * 200 get, update, delete
 * 422 data invalid or validation error
 */
class ApiProductTest extends TestCase
{
    protected $endpoint = 'api/products';

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
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

    private $fields = [
        'id',
        'name',
        'description',
        'stock',
        'category_id',
        'created_at'
    ];

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
                    'data' => $this->fields,
                ]
            );
    }

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
                    'data' => $this->fields,
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
                    'data' => $this->fields,
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
                    'data' => $this->fields,
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
                    'data' => $this->fields,
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
                    'data' => $this->fields,
                ]
            );

        $this->assertCount(1, Product::all());
        $product = Product::first();

        $response = $this->deleteJson($this->endpoint . "/{$product->id}");
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
                    'data' => $this->fields,
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

    /** @test */
    public function unautorized()
    {
        factory(Product::class, 4)->create();

        $endpoint = $this->endpoint;
        $this->postJson($this->endpoint, $this->data())
            ->assertStatus(401);

        $product = Product::first();
        $endpoint = $endpoint . "/{$product->id}";

        $this->putJson($endpoint, array_merge($this->data(), [
            'name' => 'Puma Shoes',
            'description' => 'Puma Description'
        ]))->assertStatus(401);

        $this->deleteJson($this->endpoint . "/{$product->id}")
            ->assertStatus(401);
    }

    /** @test */
    public function autorized()
    {
        $login = ['email' => 'doe@example.com', 'password' => 'aziz'];

        $response = $this->postJson('api/login', $login);
        $response->assertStatus(200)
            ->assertJsonStructure([
                "user" => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ],
                 "access_token",
                 "message"
             ]);
        $this->assertAuthenticated();

        factory(Product::class, 4)->create();

        $endpoint = $this->endpoint;
        $this->postJson($this->endpoint, $this->data(), ['Authorization' => 'Bearer ' . $response['access_token']])
            ->assertStatus(201);

        $product = Product::first();
        $endpoint = $endpoint . "/{$product->id}";

        $this->putJson($endpoint, array_merge($this->data(), [
            'name' => 'Puma Shoes',
            'description' => 'Puma Description'
        ], ['Authorization' => 'Bearer ' . $response['access_token']]))->assertStatus(200);

        $this->deleteJson($this->endpoint . "/{$product->id}", [], ['Authorization' => 'Bearer ' . $response['access_token']])
            ->assertStatus(200);
    }
}
