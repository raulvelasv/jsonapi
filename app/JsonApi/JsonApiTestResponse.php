<?php

namespace App\JsonApi;

use PHPUnit\Framework\ExpectationFailedException;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Str;
use PHPUnit\Framework\Assert as PHPUnit;
use Closure;

class JsonApiTestResponse
{
    public function assertJsonApiValidationErrors(): Closure
    {
        return function ($attribute) {
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
        };
    }
}
