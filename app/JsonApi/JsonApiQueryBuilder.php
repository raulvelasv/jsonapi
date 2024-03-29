<?php

namespace App\JsonApi;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Closure;

class JsonApiQueryBuilder
{
    public function allowedSorts(): Closure
    {
        return function ($allowedSorts) {
            /**
             * @var Builder $this
             */
            if (request()->filled('sort')) {
                $sortFileds = explode(',', request()->input('sort'));
                foreach ($sortFileds as $sortField) {
                    $sortDirection = Str::of($sortField)->startsWith('-') ? 'desc' : 'asc';
                    $sortField = ltrim($sortField, '-');
                    abort_unless(in_array($sortField, $allowedSorts), 400);

                    $this->orderBy($sortField, $sortDirection);
                }
            }
            return $this;
        };
    }

    public function allowedFilters(): Closure
    {
        return function ($allowedFilters) {
            /** @var Builder $this */
            foreach (request('filter', []) as $filter => $value) {
                abort_unless(in_array($filter, $allowedFilters), 400);
                $this->hasNamedScope($filter) ?
                    $this->{$filter}($value) :
                    $this->where($filter, 'LIKE', '%' . $value . '%');
            };
            return $this;
        };
    }

    public function jsonPaginate(): Closure
    {
        return function () {
            /** @var Builder $this */
            return $this->paginate(  // en este contexto $this es la clase que estamos extendiendo y debemos retornarlo
                $perPage = request('page.size', 15),
                $columns = ['*'],
                $pageName = 'page[number]',
                $page = request('page.number', 1),
            )->appends(request()->only('sort', 'filter', 'page.size'));
        };
    }
}
