<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RssFeedController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
Route::get('/search', [PostController::class, 'search'])->name('search');
Route::get('/posts/{slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/tags/{slug}', [TagController::class, 'show'])->name('tags.show');
Route::get('/p/{slug}', [PageController::class, 'show'])->name('pages.show');

// --- Comment store (public + auth) ---
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
    ->middleware('throttle:comments')
    ->name('comments.store');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Bookmarks
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/posts/{post}/bookmark', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');

    // Comment likes
    Route::post('/comments/{comment}/like', [CommentLikeController::class, 'toggle'])->name('comments.like');
});

// --- Admin routes ---
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('posts', Admin\PostController::class)->except(['show']);
    Route::resource('collections', Admin\CollectionController::class)->except(['show']);
    Route::resource('categories', Admin\CategoryController::class)->except(['show', 'create', 'edit']);
    Route::resource('tags', Admin\TagController::class)->only(['index', 'store', 'destroy']);
    Route::resource('pages', Admin\PageController::class)->except(['show']);
    Route::resource('menu-items', Admin\MenuController::class)->only(['store', 'update', 'destroy']);
    Route::get('menus', [Admin\MenuController::class, 'index'])->name('menus.index');
    Route::post('menus/reorder', [Admin\MenuController::class, 'reorder'])->name('menus.reorder');

    Route::get('cache', [Admin\CacheController::class, 'index'])->name('cache.index');
    Route::post('cache/clear', [Admin\CacheController::class, 'clear'])->name('cache.clear');

    Route::get('comments', [Admin\CommentController::class, 'index'])->name('comments.index');
    Route::patch('comments/{comment}/approve', [Admin\CommentController::class, 'approve'])->name('comments.approve');
    Route::delete('comments/{comment}', [Admin\CommentController::class, 'destroy'])->name('comments.destroy');
});

require __DIR__.'/auth.php';
