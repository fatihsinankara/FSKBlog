<?php

namespace App\Providers;

use App\Contracts\NewsletterSync;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Policies\CategoryPolicy;
use App\Policies\CommentPolicy;
use App\Policies\PostPolicy;
use App\Policies\TagPolicy;
use App\Services\NullNewsletterSync;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\MarkdownConverter;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(NewsletterSync::class, NullNewsletterSync::class);

        $this->app->singleton(MarkdownConverter::class, function () {
            $config = [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
                'heading_permalink' => [
                    'html_class' => 'heading-permalink',
                    'insert' => 'before',
                    'symbol' => '#',
                ],
            ];

            $environment = new Environment($config);
            $environment->addExtension(new CommonMarkCoreExtension);
            $environment->addExtension(new GithubFlavoredMarkdownExtension);
            $environment->addExtension(new HeadingPermalinkExtension);

            return new MarkdownConverter($environment);
        });
    }

    public function boot(): void
    {
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Tag::class, TagPolicy::class);

        RateLimiter::for('comments', function (Request $request) {
            $message = ['body' => 'Çok hızlı yorum gönderiyorsunuz, lütfen biraz bekleyin.'];
            $response = fn () => back()->withErrors($message)->withInput($request->except('website'));

            if ($request->user()?->is_admin) {
                return Limit::perMinute(60)->by('admin:'.$request->user()->id)->response($response);
            }

            if ($request->user()) {
                return Limit::perMinute(10)->by('user:'.$request->user()->id)->response($response);
            }

            return Limit::perMinute(5)->by('ip:'.$request->ip())->response($response);
        });

        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        Vite::prefetch(concurrency: 3);

        Category::resolveRelationUsing('postsCount', function ($category) {
            return $category->posts()->selectRaw('category_id, count(*) as aggregate')
                ->groupBy('category_id');
        });
    }
}
