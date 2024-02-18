<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Http\Resources\ArticleCollection;

class ArticleController extends Controller
{
    public function show(Article $article): ArticleResource
    {
        return ArticleResource::make($article);
    }
    public function index()
    {
        $articles = Article::all();
        return ArticleCollection::make($articles);
    }
}
