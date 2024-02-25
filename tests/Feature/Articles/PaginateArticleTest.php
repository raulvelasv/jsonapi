<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaginateArticleTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function can_paginate_articles(): void
    {
        $articles = Article::factory()->count(6)->create();
        $url = route('api.v1.articles.index', [
            'page' => [
                'number' => 2,
                'size' => 2
            ]
        ]);
        $response = $this->getJson($url);

        $response->assertSee([
            $articles[2]->title,
            $articles[3]->title
        ]);
        $response->assertDontSee([
            $articles[0]->title,
            $articles[1]->title,
            $articles[4]->title,
            $articles[5]->title
        ]);
        $response->assertJsonStructure([
            'links' => ['first', 'last', 'prev', 'next'],
        ]);
        $firstLink = urldecode($response->json('links.first'));
        $lastLink = urldecode($response->json('links.last'));
        $prevLink = urldecode($response->json('links.prev'));
        $nextLink = urldecode($response->json('links.next'));

        $this->assertStringContainsString('page[number]=1', $firstLink);
        $this->assertStringContainsString('page[size]=2', $firstLink);

        $this->assertStringContainsString('page[number]=3', $lastLink);
        $this->assertStringContainsString('page[size]=2', $lastLink);

        $this->assertStringContainsString('page[number]=1', $prevLink);
        $this->assertStringContainsString('page[size]=2', $prevLink);

        $this->assertStringContainsString('page[number]=3', $nextLink);
        $this->assertStringContainsString('page[size]=2', $nextLink);
    }
    /** @test */
    public function can_paginate_articles_sorted_articles(): void
    {
        Article::factory()->create(['title' => 'C Title']);
        Article::factory()->create(['title' => 'A Title']);
        Article::factory()->create(['title' => 'B Title']);

        $url = route('api.v1.articles.index', [
            'sort' => 'title',
            'page' => [
                'size' => 1,
                'number' => 2,
            ]
        ]);

        $response = $this->getJson($url);

        $response->assertSee([
            'B Title'
        ]);

        $response->assertDontSee([
            'A Title',
            'C Title'
        ]);

        $firstLink = urldecode($response->json('links.first'));
        $lastLink = urldecode($response->json('links.last'));
        $prevLink = urldecode($response->json('links.prev'));
        $nextLink = urldecode($response->json('links.next'));

        $this->assertStringContainsString('sort=title', $firstLink);
        $this->assertStringContainsString('sort=title', $lastLink);
        $this->assertStringContainsString('sort=title', $prevLink);
        $this->assertStringContainsString('sort=title', $nextLink);
    }
    /** @test */
    public function can_paginate_filtered_articles(): void
    {
        Article::factory(3)->create();
        // Paso 1: creamos 3 artículos con los factory de modo que podamos comprobar posteriormente la
        // ordenación
        Article::factory()->create(['title' => "C Laravel"]);
        Article::factory()->create(['title' => "A Laravel"]);
        Article::factory()->create(['title' => "B Laravel"]);
        // Paso 2. Definimos la ruta con los parámetros correspondientes
        $url = route('api.v1.articles.index', [
            'filter[title]' => 'laravel',
            'page' => [
                'size' => 1,
                'number' => 2
            ]
        ]);

        //        dd(urldecode($url)); // Inspeccionamos la variable, que debería contener el string
        //          articles?filter[title]=laravel&page['size']=1&page['number']=2
        // según especificación json:qpi

        // Paso 3. Creamos la petición. Las cabeceras las añade el trait MakesJsonApiRequests
        $response = $this->getJson($url);

        // Hacemos las comprobaciones (asserts) siguientes
        $firstLink = urldecode($response->json('links.first'));
        $lastLink = urldecode($response->json('links.last'));
        $prevLink = urldecode($response->json('links.prev'));
        $nextLink = urldecode($response->json('links.next'));

        // comprobamos que en estos cuatro enlaces aparece el parámetro 'sort=title'
        // Los parámetros page['size'] y page['number'] se comprobaron en el método anterior
        $this->assertStringContainsString('filter[title]=laravel', $firstLink);
        $this->assertStringContainsString('filter[title]=laravel', $lastLink);
        $this->assertStringContainsString('filter[title]=laravel', $prevLink);
        $this->assertStringContainsString('filter[title]=laravel', $nextLink);
    }
}
