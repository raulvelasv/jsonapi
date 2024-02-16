<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListArticlesTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function can_fetch_a_single_article()
    {
        $this->withoutExceptionHandling();
        $article = Article::factory()->create();
        // si hacemos un dump en la respuesta, vemos la respuesta json api
        $response = $this->getJson(route('api.v1.articles.show', $article))->dump();

        $response->assertSee($article->title);

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'content' => $article->content
                ],
                'links' => [
                    'self' => url('/api/v1/articles/' . $article->getRouteKey())
                ]

            ]
        ]);
    }
}
