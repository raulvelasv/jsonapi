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
        $this->withoutExceptionHandling();
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
}
