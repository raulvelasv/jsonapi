<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_filter_articles_by_title(): void
    {
        Article::factory()->create([
            'title' => 'Aprende Laravel desde cero'
        ]);

        Article::factory()->create([
            'title' => 'Other Article'
        ]);
        // articles?filter[title]=Laravel
        $url = route('api.v1.articles.index', [
            'filter' =>
            [
                'title' => 'Laravel'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprende Laravel desde cero')
            ->assertDontSee('Other Article');
    }

    /** @test */
    public function can_filter_articles_by_content(): void
    {
        Article::factory()->create(['content' => 'Aprende Laravel desde cero']);

        Article::factory()->create(['content' => 'Other Article']);

        $url = route('api.v1.articles.index', [
            'filter' =>
            [
                'content' => 'Laravel'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprende Laravel desde cero')
            ->assertDontSee('Other Article');
    }

    /** @test */
    public function can_filter_articles_by_year(): void
    {
        Article::factory()->create([
            'title' =>  'Article from 2021',
            'created_at' => now()->year(2021)
        ]);

        Article::factory()->create([
            'title' =>  'Article from 2022',
            'created_at' => now()->year(2022)
        ]);
        // articles?filter[year]=2021
        $url = route('api.v1.articles.index', [
            'filter' =>
            [
                'year' => '2021'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Article from 2021')
            ->assertDontSee('Article from 2022');
    }

    /** @test */
    public function can_filter_articles_by_month(): void
    {
        Article::factory()->create([
            'title' =>  'Article from month 3',
            'created_at' => now()->month(3)
        ]);

        Article::factory()->create([
            'title' =>  'Another article from month 3',
            'created_at' => now()->month(3)
        ]);

        Article::factory()->create([
            'title' =>  'Another article from month 1',
            'created_at' => now()->month(1)
        ]);
        // articles?filter[month]=3
        $url = route('api.v1.articles.index', [
            'filter' =>
            [
                'month' => '3'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(2, 'data')
            ->assertSee('Article from month 3')
            ->assertSee('Another article from month 3')
            ->assertDontSee('Another article from month 1');
    }

    /** @test */
    public function cannot_filter_articles_by_unknown_filters(): void
    {

        Article::factory(2)->create();
        // articles?filter[unknown]=filter
        $url = route('api.v1.articles.index', [
            'filter' =>
            [
                'unknown' => 'filter'
            ]
        ]);

        $this->getJson($url)
            ->assertStatus(400);
    }
}
