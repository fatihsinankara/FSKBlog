<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CommentController extends Controller
{
    public function index(): Response
    {
        $pending = Comment::with(['post:id,title,slug', 'user:id,name'])
            ->where('is_approved', false)
            ->latest()
            ->paginate(20, ['*'], 'pending_page');

        $approved = Comment::with(['post:id,title,slug', 'user:id,name'])
            ->where('is_approved', true)
            ->latest()
            ->paginate(20, ['*'], 'approved_page');

        return Inertia::render('Admin/Comments/Index', [
            'pending'  => $pending,
            'approved' => $approved,
        ]);
    }

    public function approve(Comment $comment): RedirectResponse
    {
        $comment->update(['is_approved' => true]);

        return back()->with('message', 'Yorum onaylandı.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('message', 'Yorum silindi.');
    }
}
