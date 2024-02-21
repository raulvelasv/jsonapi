<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ValidateJsonApiHeader;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidateJsonApiHeadersTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function accept_header_must_be_present_in_all_requests()
    {
        Route::get('test_route', function () {
            return 'OK';
        })->middleware(ValidateJsonApiHeader::class);
        $this->get('test_route')->assertStatus(406);
        $this->get('test_route', [
            'accept' => 'application/vnd.api+json'
        ])->assertSuccessful();
    }
    /** @test */
    public function content_type_header_must_be_present_on_all_posts_requests()
    {
        Route::post('test_route', function () {
            return 'OK';
        })->middleware(ValidateJsonApiHeader::class);
        $this->post('test_route', [], [
            'accept' => 'application/vnd.api+json'
        ])->assertStatus(415);
        $this->post('test_route', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json'
        ])->assertSuccessful();
    }
    /** @test */
    public function content_type_header_must_be_present_on_all_patch_requests()
    {
        Route::patch('test_route', function () {
            return 'OK';
        })->middleware(ValidateJsonApiHeader::class);
        $this->patch('test_route', [], [
            'accept' => 'application/vnd.api+json'
        ])->assertStatus(415);
        $this->patch('test_route', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json'
        ])->assertSuccessful();
    }
    /** @test */
    function content_type_header_must_be_present_in_responses()
    {
        Route::any('test_route', function () {
            return 'OK';
        })->middleware(ValidateJsonApiHeader::class);
        $this->get('test_route')->assertStatus(406);
        $this->get('test_route', [
            'accept' => 'application/vnd.api+json'
        ])->assertHeader('content-type', 'application/vnd.api+json');

        $this->post('test_route', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json'
        ])->assertHeader('content-type', 'application/vnd.api+json');
        $this->patch('test_route', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json'
        ])->assertHeader('content-type', 'application/vnd.api+json');
    }
    /** @test */
    function content_type_header_must_be_present_in_empty_responses()
    {
        Route::any('empty_response', function () {
            return response()->noContent();
        })->middleware(ValidateJsonApiHeader::class);
        $this->get('empty_response', [
            'accept' => 'application/vnd.api+json'
        ])->assertHeaderMissing('content-type');
        $this->post('empty_response', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json'
        ])->assertHeaderMissing('content-type');
        $this->patch('empty_response', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json'
        ])->assertHeaderMissing('content-type');
        $this->delete('empty_response', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json'
        ])->assertHeaderMissing('content-type');
    }
}
