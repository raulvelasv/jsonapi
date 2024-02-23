<?php

namespace Tests;

use Closure;
use Illuminate\Testing\TestResponse;

trait MakesJsonApiRequests
{
    protected function setUp(): void
    {
        parent::setUp();
        TestResponse::macro(
            'assertJsonApiValidationErrors',
            $this->assertJsonApiValidationErrors()
        );
    }
    protected function assertJsonApiValidationErrors(): Closure
    {
        return function ($attribute) {
            $this->assertJsonStructure([
                'errors' => [
                    ['title', 'detail', 'source' => ['pointer']]
                ]
            ])->assertJsonFragment([
                'source' => ['pointer' => '/data/attributes/' . $attribute]
            ])->assertHeader('content-type', 'application/vnd.api+json')
                ->assertStatus(422);
        };
    }

    public function json($method, $uri, array $data = [], array $headers = [], $options = 0): \Illuminate\Testing\TestResponse
    {
        $headers['accept'] = 'application/vnd.api+json';
        return parent::json($method, $uri, $data, $headers);
    }
    public function postJson($uri, array $data = [], array $headers = [], $options = 0): \Illuminate\Testing\TestResponse
    {
        $headers['content-type'] = 'application/vnd.api+json';
        return parent::postJson($uri, $data, $headers);
    }
    public function patchJson($uri, array $data = [], array $headers = [], $options = 0): \Illuminate\Testing\TestResponse
    {
        $headers['content-type'] = 'application/vnd.api+json';
        return parent::patchJson($uri, $data, $headers);
    }
}
