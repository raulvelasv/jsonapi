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
        $articles = Article::query();

        $allowedFilters = ['title', 'content', 'year', 'month'];

        foreach (request('filter', []) as $filter => $value) {
            abort_unless(in_array($filter, $allowedFilters), 400);
            if ($filter === 'year')
                $articles->whereYear('created_at', $value);
            elseif ($filter === 'month')
                $articles->whereMonth('created_at', $value);
            else
                $articles->where($filter, 'LIKE', '%' . $value . '%');
        }

        $articles->allowedSorts(['title', 'content']);

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
