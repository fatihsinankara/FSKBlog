<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Notifications\CommentReplyNotification;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CommentController extends Controller
{
    public function index(): Response
    {
        $pending = Comment::with(['post:id,title,slug', 'user:id,name', 'parent.user:id,name'])
            ->where('is_approved', false)
            ->latest()
            ->paginate(20, ['*'], 'pending_page');

        $approved = Comment::with(['post:id,title,slug', 'user:id,name', 'parent.user:id,name'])
            ->where('is_approved', true)
            ->latest()
            ->paginate(20, ['*'], 'approved_page');

        return Inertia::render('Admin/Comments/Index', [
            'pending' => $pending,
            'approved' => $approved,
        ]);
    }

    public function approve(Comment $comment): RedirectResponse
    {
        $comment->update(['is_approved' => true]);
        $comment->loadMissing('post', 'parent.user');

        if ($comment->parent?->user && $comment->parent->user_id !== $comment->user_id) {
            $comment->parent->user->notify(new CommentReplyNotification($comment));
        }

        return back()->with('message', 'Yorum onaylandı.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('message', 'Yorum silindi.');
    }
}
