<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ValidateJsonApiDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidateJsonApiDocumentTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    protected function setUp(): void
    {
        parent::setUp();
        Route::any('test_route', function () {
            return 'OK';
        })->middleware(ValidateJsonApiDocument::class);
    }

    /** @test */
    public function data_is_required()
    {
        $this->postJson('test_route', [])
            ->dump()
            ->assertJsonApiValidationErrors('data');
        $this->patchJson('test_route', [])
            ->dump()
            ->assertJsonApiValidationErrors('data');
    }
    /** @test */
    public function data_must_be_array()
    {
        $this->postJson('test_route', [
            'data' => 'string'
        ])
            ->dump()
            ->assertJsonApiValidationErrors('data');
        $this->patchJson('test_route', [
            'data' => 'string'
        ])
            ->dump()
            ->assertJsonApiValidationErrors('data');
    }
    /** @test */
    public function data_type_is_required()
    {
        $this->postJson('test_route', [
            'data' => [
                'attributes' => []
            ]
        ])
            ->assertJsonApiValidationErrors('data.type');
        $this->patchJson('test_route', [
            'data' => [
                'attributes' => []
            ]
        ])
            ->assertJsonApiValidationErrors('data.type');
    }
    /** @test */
    public function data_type_must_be_string()
    {
        $this->postJson('test_route', [
            'data' => [
                'type' => 1
            ]
        ])
            ->assertJsonApiValidationErrors('data.type');
        $this->patchJson('test_route', [
            'data' => [
                'type' => 1
            ]
        ])
            ->assertJsonApiValidationErrors('data.type');
    }
}
