<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleCollection;

class ArticleController extends Controller
{
    //public $collects = ArticlesResource::class; si quisieramos que se llamara asi porque
    // el ArticleCollection siempre va a buscar el ArticleResource por convencion
    public function show(Article $article): ArticleResource
    {
        return ArticleResource::make($article);
    }
    public function index()
    {
        $articles = Article::all();
        return ArticleCollection::make($articles);
    }
    public function store(SaveArticleRequest $request): ArticleResource
    {
        $article = Article::create([
            'title' => $request->input('data.attributes.title'),
            'slug' => $request->input('data.attributes.slug'),
            'content' => $request->input('data.attributes.content')
        ]);
        return ArticleResource::make($article);
    }
    public function update(Article $article, SaveArticleRequest $request): ArticleResource
    {
        $article->update([
            'title' => $request->input('data.attributes.title'),
            'slug' => $request->input('data.attributes.slug'),
            'content' => $request->input('data.attributes.content')
        ]);
        return ArticleResource::make($article);
    }
    public function destroy(Article $article): \Illuminate\Http\Response
    {
        $article->delete();
        return response()->noContent();
    }
}
