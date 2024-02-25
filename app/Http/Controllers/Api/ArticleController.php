<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\SaveArticleRequest;
use App\Http\Resources\ArticleCollection;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ArticleController extends Controller
{
    //public $collects = ArticlesResource::class; si quisieramos que se llamara asi porque
    // el ArticleCollection siempre va a buscar el ArticleResource por convencion
    public function show(Article $article): ArticleResource
    {
        return ArticleResource::make($article);
    }
    public function index(): ArticleCollection
    {
        $articles = Article::allowedSorts(['title', 'content']);
        return ArticleCollection::make($articles->jsonPaginate());
    }
    public function store(SaveArticleRequest $request): ArticleResource
    {
        $article = Article::create($request->validated());
        return ArticleResource::make($article);
    }
    public function update(Article $article, SaveArticleRequest $request): ArticleResource
    {
        $article->update($request->validated());
        return ArticleResource::make($article);
    }
    public function destroy(Article $article): \Illuminate\Http\Response
    {
        $article->delete();
        return response()->noContent();
    }
}
