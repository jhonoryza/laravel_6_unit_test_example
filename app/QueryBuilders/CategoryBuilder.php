<?php

namespace App\QueryBuilders;

use App\Http\Requests\CategoryGetRequest;
use App\Category;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryBuilder extends BaseQueryBuilder
{
    /**
     * CategoryBuilder constructor.
     *
     * @param CategoryGetRequest $request
     */
    public function __construct(CategoryGetRequest $request)
    {
        $this->request = $request;
        $this->builder = QueryBuilder::for(Category::class, $request);
    }

    /**
     * Get a list of allowed columns that can be selected.
     *
     * @return array
     */
    protected function getAllowedFields(): array
    {
        return [
            'categories.id',
            'categories.name',
            'categories.parent_id',
            'categories.created_at',
            'categories.updated_at',
        ];
    }

    /**
     * Get a list of allowed columns that can be used in any filter operations.
     *
     * @return array
     */
    protected function getAllowedFilters(): array
    {
        return [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('name'),
            AllowedFilter::exact('parent_id'),
            AllowedFilter::exact('created_at'),
            AllowedFilter::exact('updated_at'),
        ];
    }

    /**
     * Get a list of allowed columns that can be used in any sort operations.
     *
     * @return array
     */
    protected function getAllowedSorts(): array
    {
        return [
            'id',
            'name',
            'parent_id',
            'created_at',
            'updated_at',
        ];
    }
}
