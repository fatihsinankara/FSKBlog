<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\NewsletterSubscriptionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RssFeedController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

// --- RSS Feed ---
Route::get('/feed.xml', [RssFeedController::class, 'index'])->name('feed');

// --- Sitemap ---
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap/pages.xml', [SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap/posts.xml', [SitemapController::class, 'posts'])->name('sitemap.posts');
Route::get('/sitemap/categories.xml', [SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('/sitemap/tags.xml', [SitemapController::class, 'tags'])->name('sitemap.tags');

// --- Public routes ---
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/search', [PostController::class, 'search'])->middleware('throttle:search')->name('search');
Route::get('/posts/{slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/tags/{slug}', [TagController::class, 'show'])->name('tags.show');
Route::get('/collections/{slug}', [CollectionController::class, 'show'])->name('collections.show');
Route::get('/p/{slug}', [PageController::class, 'show'])->name('pages.show');
Route::post('/newsletter/subscribe', [NewsletterSubscriptionController::class, 'store'])
    ->middleware('throttle:newsletter')
    ->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterSubscriptionController::class, 'confirm'])->name('newsletter.unsubscribe');
Route::post('/newsletter/unsubscribe/{token}', [NewsletterSubscriptionController::class, 'destroy'])
    ->middleware('throttle:newsletter')
    ->name('newsletter.unsubscribe.destroy');

// --- Comment store (public + auth) ---
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
    ->middleware('throttle:comments')
    ->name('comments.store');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/settings', [ProfileController::class, 'edit'])->name('settings.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Bookmarks
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/posts/{post}/bookmark', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    Route::patch('/bookmarks/{bookmark}/status', [BookmarkController::class, 'updateStatus'])->name('bookmarks.status');

    // Comment likes
    Route::post('/comments/{comment}/like', [CommentLikeController::class, 'toggle'])->name('comments.like');

    // Follows
    Route::post('/categories/{category}/follow', [FollowController::class, 'toggleCategory'])->name('categories.follow');
    Route::post('/collections/{collection}/follow', [FollowController::class, 'toggleCollection'])->name('collections.follow');
});

// --- Admin routes ---
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('posts', Admin\PostController::class)->except(['show']);
    Route::resource('collections', Admin\CollectionController::class)->except(['show']);
    Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
    Route::post('settings', [Admin\SettingController::class, 'update'])->name('settings.update');
    Route::resource('categories', Admin\CategoryController::class)->except(['show']);
    Route::resource('tags', Admin\TagController::class)->except(['show']);
    Route::resource('pages', Admin\PageController::class)->except(['show']);
    Route::resource('menus', Admin\MenuController::class)
        ->except(['show'])
        ->parameters(['menus' => 'menuItem']);
    Route::post('menus/reorder', [Admin\MenuController::class, 'reorder'])->name('menus.reorder');
    Route::resource('users', Admin\UserController::class)->except(['show']);

    Route::post('upload/image', [Admin\UploadController::class, 'image'])->name('upload.image');

    Route::get('cache', [Admin\CacheController::class, 'index'])->name('cache.index');
    Route::post('cache/clear', [Admin\CacheController::class, 'clear'])->name('cache.clear');

    Route::get('cron', [Admin\CronController::class, 'index'])->name('cron.index');
    Route::post('cron/ping', [Admin\CronController::class, 'ping'])->name('cron.ping');

    Route::get('comments', [Admin\CommentController::class, 'index'])->name('comments.index');
    Route::patch('comments/{comment}/approve', [Admin\CommentController::class, 'approve'])->name('comments.approve');
    Route::delete('comments/{comment}', [Admin\CommentController::class, 'destroy'])->name('comments.destroy');
});

require __DIR__.'/auth.php';
