<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class FetchArticlesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->fetchFromNewsApi();
        $this->fetchFromNYTApi();
        $this->theGuardianApi();
    }

    protected function fetchFromNewsApi()
    {
        $newskey = env('NEWS_API_KEY');
        $response = Http::get('https://newsapi.org/v2/top-headlines', [
            'country' => 'us',
            'apiKey' => $newskey
        ]);

        $news = $response->json();
        $newsdata = $news['articles'];

        foreach ($newsdata as $article) {
            $this->saveArticle($article);
        }
    }

    protected function fetchFromNYTApi()
    {
        $nytApiKey = env('NYT_API_KEY');
        $response = Http::get('https://api.nytimes.com/svc/news/v3/content/all/all.json', [
            'country' => 'us',
            'api-key' => $nytApiKey
        ]);

        $news = $response->json();
        $newsdata = $news['results'];
        foreach ($newsdata as $article) {
            $this->saveArticle($article);
        }
    }

    protected function theGuardianApi()
    {
        $GuardianApiKey = env('THE_GUARDIAN_API_KEY');
        $response = Http::get('https://content.guardianapis.com/search', [
            'api-key' => $GuardianApiKey
        ]);

        $news = $response->json();
        $newsData = $news['response']['results'];
        foreach ($newsData as $article) {
            $this->saveArticle($article);
        }
    }


    protected function saveArticle($article)
    {

        $source = Source::firstOrCreate(['name' => $article['source']['name'] ?? $article['source'] ?? $article['sectionName']]);
        $author = Category::firstOrCreate(['name' => $article['type'] ?? $article['item_type'] ?? 'Other']);
        $author = Author::firstOrCreate(['name' => $article['author'] ?? $article['byline'] ?? 'Unknown']);

        if (isset($article['publishedAt']) && $article['publishedAt'] === '1970-01-01T00:00:00Z') {
            $publishedAt = now();
        } else {
            $publishedAt = Carbon::parse($article['publishedAt'] ?? $article['published_date'] ?? $article['webPublicationDate']);
        }

        Article::updateOrCreate(
            ['title' => $article['title'] ?? $article['webTitle'] ],
            [
                'content' => $article['description'] ?? ($article['abstract'] ?? ''),
                'published_at' => $publishedAt,
                'source_id' => $source->id,
                'author_id' => $author->id,
                'news_link' => $article['url'] ?? $article['webUrl'],
                'image_url' => $article['urlToImage'] ?? $article['multimedia']['url'] ?? null,
            ]
        );
    }
}
