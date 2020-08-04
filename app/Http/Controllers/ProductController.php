<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Product;

class ProductController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function store()
    {
        $data = request()->validate($this->rules());
        Product::create($data);
        return redirect()->route('products.index');
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
        return redirect()->route('products.index');
    }

    public function delete(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index');
    }
}
