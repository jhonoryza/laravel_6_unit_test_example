<?php

namespace App\QueryBuilders;

use App\Http\Requests\ProductGetRequest;
use App\Product;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductBuilder extends BaseQueryBuilder
{
    /**
     * ProductBuilder constructor.
     *
     * @param ProductGetRequest $request
     */
    public function __construct(ProductGetRequest $request)
    {
        $this->request = $request;
        $this->builder = QueryBuilder::for(Product::class, $request);
    }

    /**
     * Get a list of allowed columns that can be selected.
     *
     * @return array
     */
    protected function getAllowedFields(): array
    {
        return [
            'products.id',
            'products.name',
            'products.description',
            'products.stock',
            'products.category_id',
            'products.created_at',
            'products.updated_at',
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
            AllowedFilter::exact('description'),
            AllowedFilter::exact('stock'),
            AllowedFilter::exact('category_id'),
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
            'description',
            'stock',
            'category_id',
            'created_at',
            'updated_at',
        ];
    }
}
