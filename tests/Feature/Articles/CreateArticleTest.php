<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function can_create_articles()
    {
        // $this->withoutExceptionHandling();
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'My first article',
                    'slug' => 'my-first-article',
                    'content' => 'content of my first article'
                ]
            ]
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
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'ab',
                    'slug' => 'my-first-article',
                    'content' => 'content of my first article'
                ]
            ]
        ])->dump();
        $response->assertJsonStructure([
            'errors' => [
                ['title', 'detail', 'source' => ['pointer']]
            ]
        ])->assertJsonFragment([
            'source' => ['pointer' => '/data/attributes/title']
        ]);
        // $response->assertJsonValidationErrors('data.attributes.title');
    }
    /** @test */
    public function title_must_be_at_least_4_characters()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'abc',
                    'slug' => 'my-first-article',
                    'content' => 'content of my first article'
                ]
            ]
        ]);
        $response->assertJsonValidationErrors('data.attributes.title');
    }
    /** @test */
    public function slug_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'My first article',
                    'content' => 'content of my first article'
                ]
            ]
        ]);
        $response->assertJsonValidationErrors('data.attributes.slug');
    }
    /** @test */
    public function content_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'My first article',
                    'slug' => 'my-first-article',
                ]
            ]
        ]);
        $response->assertJsonValidationErrors('data.attributes.content');
    }
}
