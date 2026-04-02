<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdown\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\MarkdownConverter;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MarkdownConverter::class, function () {
            $config = [
                'html_input'         => 'strip',
                'allow_unsafe_links' => false,
                'heading_permalink'  => [
                    'html_class' => 'heading-permalink',
                    'insert'     => 'before',
                    'symbol'     => '#',
                ],
            ];

            $environment = new Environment($config);
            $environment->addExtension(new CommonMarkCoreExtension());
            $environment->addExtension(new GithubFlavoredMarkdownExtension());
            $environment->addExtension(new HeadingPermalinkExtension());

            return new MarkdownConverter($environment);
        });
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
