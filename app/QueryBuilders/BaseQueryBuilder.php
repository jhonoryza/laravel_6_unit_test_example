<?php

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

abstract class BaseQueryBuilder
{
    /**
     * The Query Builder instance for the current resource list generator.
     *
     * @var QueryBuilder
     */
    protected $builder;

    /**
     * Current HTTP Request object.
     *
     * @var FormRequest
     */
    protected $request;

    public function __construct(FormRequest $request, Model $model)
    {
        $this->request = $request;
        $this->builder = QueryBuilder::for($model, $request);
    }

    /**
     * Get the default sort column that will be used in any sort operation.
     *
     * @return string
     */
    protected function getDefaultSort(): string
    {
        return 'id';
    }

    /**
     * Get the query builder instance.
     *
     * @return QueryBuilder
     */
    public function getBuilder(): QueryBuilder
    {
        $search = $this->request->input('search');

        return ($search === null) ? $this->builder : $this->builder->whereLike($this->getAllowedSearch(), $search);  // @phpstan-ignore-line
    }

    /**
     * Find the banner model based on the given id number.
     *
     * @param int $key
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     *
     * @return mixed
     */
    public function find(int $key)
    {
        return $this->query()->findOrFail($key);
    }

    /**
     * Get the paginated results of current API get request.
     *
     * @return LengthAwarePaginator
     */
    public function paginate(): LengthAwarePaginator
    {
        return $this->query()->jsonPaginate();  // @phpstan-ignore-line
    }

    /**
     * Get default query builder.
     *
     * @return QueryBuilder
     */
    protected function query(): QueryBuilder
    {
        return $this->getBuilder()
            ->allowedFields($this->getAllowedFields())
            ->allowedFilters($this->getAllowedFilters())
            ->allowedSorts($this->getAllowedSorts())
            ->defaultSort($this->getDefaultSort())
            ->allowedIncludes($this->getAllowedIncludes());
    }

    /**
     * Get a list of allowed columns that can be used in any sort operations.
     *
     * @return array
     */
    protected function getAllowedSorts(): array
    {
        return [];
    }

    /**
     * Get a list of allowed columns that can be used in any filter operations.
     *
     * @return array
     */
    protected function getAllowedFilters(): array
    {
        return [];
    }

    /**
     * Get a list of allowed columns that can be used in any filter operations.
     *
     * @return array
     */
    protected function getAllowedFields(): array
    {
        return [];
    }

    /**
     * Get a list of allowed columns that can be used in any filter operations.
     *
     * @return array
     */
    protected function getAllowedIncludes(): array
    {
        return [];
    }

    /**
     * Get a list of allowed searchable columns which can be used in any search operations.
     *
     * @return string[]
     */
    protected function getAllowedSearch(): array
    {
        return [
            'id',
        ];
    }
}
