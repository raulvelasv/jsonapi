<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class DeleteArticleTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function guest_cannot_delete_articles(): void
    {
        $article = Article::factory()->create();

        $this->deleteJson(route('api.v1.articles.destroy', $article))
            ->assertUnauthorized();
    }
    /** @test */
    public function can_delete_articles(): void
    {

        $articles = Article::factory()->create();
        Sanctum::actingAs($articles->user);
        $this->deleteJson(route('api.v1.articles.destroy', $articles))
            ->assertNoContent();
        $this->assertDatabaseCount('articles', 0);
    }
}
