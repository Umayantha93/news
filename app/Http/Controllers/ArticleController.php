<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\ArticleFilter;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        return ArticleFilter::apply($request);
    }

    public function show($id)
    {
        return Article::findOrFail($id);
    }
}
