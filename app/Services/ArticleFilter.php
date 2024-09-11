<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticleFilter
{
    public static function apply(Request $request)
    {
        $query = Article::query();

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        if ($request->filled('source')) {
            $query->whereHas('source', function ($q) use ($request) {
                $q->where('name', $request->source);
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('published_at', $request->date);
        }

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('content', 'like', '%' . $request->q . '%');
            });
        }

        return $query->paginate(20);
    }
}
