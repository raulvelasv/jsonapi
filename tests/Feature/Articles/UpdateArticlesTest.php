<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateArticlesTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function can_update_articles()
    {
        $article = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update', $article, $article), [
            'title' => 'Update Article',
            'slug' => $article->slug,
            'content' => 'update article'
        ]);
        $response->assertOk();
        $response->assertHeader(
            'Location',
            route('api.v1.articles.show', $article)
        );
        $response->assertJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => 'Update Article',
                    'slug' => $article->slug,
                    'content' => 'update article'
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
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update', $article), [

            'slug' => 'update-article',
            'content' => 'content of my first article'

        ])->assertJsonApiValidationErrors('title');
    }
    /** @test */
    public function title_must_be_at_least_4_characters()
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update', $article), [

            'title' => 'abc',
            'slug' => 'update-article',
            'content' => 'content of my first article'

        ])->assertJsonApiValidationErrors('title');
    }
    /** @test */
    public function slug_is_required()
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated article',
            'content' => 'content of my first article'
        ])->assertJsonApiValidationErrors('slug');
    }
    /** @test */
    public function slug_must_be_unique()
    {
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article1), [
            'title' => 'My first article',
            'slug' => $article2->slug,
            'content' => 'content of my first article'
        ])->assertJsonApiValidationErrors('slug');
    }
    /** @test */
    public function content_is_required()
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated article',
            'slug' => 'update-article',
        ])->assertJsonApiValidationErrors('content');
    }
}
