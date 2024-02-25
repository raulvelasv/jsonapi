<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Database\Query\Builder;

class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Builder::macro('allowedSorts', function ($allowedSorts) {

            if (request()->filled('sort')) {
                $sortFileds = explode(',', request()->input('sort'));
                foreach ($sortFileds as $sortField) {
                    $sortDirection = Str::of($sortField)->startsWith('-') ? 'desc' : 'asc';
                    $sortField = ltrim($sortField, '-');
                    abort_unless(in_array($sortField, $allowedSorts), 400);
                    /**
                     * @var Builder $this
                     */
                    $this->orderBy($sortField, $sortDirection);
                }
            }
            return $this;
        });
    }
}
