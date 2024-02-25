<?php

namespace App\Providers;

use Illuminate\Support\Str;
use PHPUnit\Framework\Assert as PHPUnit;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use PHPUnit\Framework\ExpectationFailedException;

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
        Builder::macro('jsonPaginate', function () {
            /**
             * @var Builder $this
             */
            return   $this->paginate(
                $perPage = request('page.size', 15),
                $columns = ['*'],
                $pageName = 'page[number]',
                $page = request('page.number', 1)
            )->appends(request()->only('sort', 'page.size'));
        });
        TestResponse::macro(
            'assertJsonApiValidationErrors',
            function ($attribute) {
                $pointer =  Str::of($attribute)->startsWith('data')
                    ? "/" . str_replace('.', '/', $attribute)
                    : "/data/attributes/{$attribute}";
                try {
                    /**
                     * @var TestResponse $this
                     */
                    $this->assertJsonFragment([
                        'source' => ['pointer' => $pointer]
                    ]);
                } catch (ExpectationFailedException $e) {
                    PHPUnit::fail('Failed to find a JSON:API validation error for key:' . $attribute
                        . PHP_EOL . PHP_EOL .
                        $e->getMessage());
                }
                try {

                    $this->assertJsonStructure([
                        'errors' => [
                            ['title', 'detail', 'source' => ['pointer']]
                        ]
                    ]);
                } catch (ExpectationFailedException $e) {
                    PHPUnit::fail('Failed to find a validate JSON:API error response'
                        . PHP_EOL . PHP_EOL .
                        $e->getMessage());
                }
                $this->assertHeader(
                    'content-type',
                    'application/vnd.api+json'
                )->assertStatus(422);
            }
        );
    }
}
