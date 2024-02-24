<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class CreateArticleTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function can_create_articles()
    {
        // $this->withoutExceptionHandling();
        $response = $this->postJson(route('api.v1.articles.store'), [
            'title' => 'My first article',
            'slug' => 'my-first-article',
            'content' => 'content of my first article'
        ]);
        $response->assertCreated();
        $article = Article::first();
        $response->assertHeader(
            'Location',
            route('api.v1.articles.show', $article)
        );
        $response->assertJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => 'My first article',
                    'slug' => 'my-first-article',
                    'content' => 'content of my first article'
                ],
                'links' => [
                    'self' => route('api.v1.articles.show', $article)
                ]
            ]
        ]);
    }

    /** @test */
    public function title_is_required()
    {
        // $this->withoutExceptionHandling();
        $this->postJson(route('api.v1.articles.store'), [

            'slug' => 'my-first-article',
            'content' => 'content of my first article'

        ])->assertJsonApiValidationErrors('title');
    }
    /** @test */
    public function title_must_be_at_least_4_characters()
    {
        $this->postJson(route('api.v1.articles.store'), [

            'title' => 'abc',
            'slug' => 'my-first-article',
            'content' => 'content of my first article'

        ])->assertJsonApiValidationErrors('title');
    }
    /** @test */
    public function slug_is_required()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'My first article',
            'content' => 'content of my first article'
        ])->assertJsonApiValidationErrors('slug');
    }
    /** @test */
    public function slug_must_be_unique()
    {
        $article = Article::factory()->create();
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'My first article',
            'slug' => $article->slug,
            'content' => 'content of my first article'
        ])->assertJsonApiValidationErrors('slug');
    }
    /** @test */
    public function slug_must_only_contain_letters_numbers_and_dashes()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'My first article',
            'slug' => '$%^&',
            'content' => 'content of my first article'
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'My first article',
            'slug' => 'my-first-article',
        ])->assertJsonApiValidationErrors('content');
    }
}
