<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Product;
use App\QueryBuilders\ProductBuilder;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(ProductBuilder $query)
    {
        return (new ProductCollection($query->paginate()))
            ->additional(['message' => 'success']);
    }

    public function show(Product $product)
    {
        return (new ProductResource($product))
            ->additional(['message' => 'success']);
    }

    public function store(Request $request)
    {
        $data = request()->validate($this->rules());
        $product = Product::create($data);

        return (new ProductResource($product))
            ->additional(['message' => 'The new product has been saved.']);
    }

    private function rules()
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable',
            'stock' => 'required|numeric',
            'category_id' => 'required'
        ];
    }

    public function update(Product $product)
    {
        $data = request()->validate($this->rules());
        $product->update($data);

        return (new ProductResource($product))
            ->additional(['message' => 'The product has been updated.']);
    }

    public function delete(Product $product)
    {
        $product->delete();

        return (new ProductResource($product))
            ->additional(['message' => 'The product has been deleted.']);
    }
}
