<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Category;
use App\QueryBuilders\CategoryBuilder;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(CategoryBuilder $query)
    {
        return (new CategoryCollection($query->paginate()))
            ->additional(['message' => 'success']);
    }

    public function show(Category $category)
    {
        return (new CategoryResource($category))
            ->additional(['message' => 'success']);
    }

    public function store(Request $request)
    {
        $data = request()->validate($this->rules());
        $category = Category::create($data);

        return (new CategoryResource($category))
            ->additional(['message' => 'The new category has been saved.']);
    }

    private function rules()
    {
        return [
            'name' => 'required|string',
            'parent_id' => 'nullable|int'
        ];
    }

    public function update(Category $category)
    {
        $data = request()->validate($this->rules());
        $category->update($data);

        return (new CategoryResource($category))
            ->additional(['message' => 'The category has been updated.']);
    }

    public function delete(Category $category)
    {
        $category->delete();

        return (new CategoryResource($category))
            ->additional(['message' => 'The category has been deleted.']);
    }
}
