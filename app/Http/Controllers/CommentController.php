<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Notifications\CommentReplyNotification;
use App\Support\PostMetrics;
use App\Support\SpamFilter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    public function store(Request $request, Post $post, PostMetrics $metrics, SpamFilter $spamFilter): RedirectResponse
    {
        if (filled($request->input('website'))) {
            throw ValidationException::withMessages([
                'body' => 'Yorum gönderilemedi.',
            ]);
        }

        $rules = [
            'body' => ['required', 'string', 'min:3', 'max:2000'],
            'website' => ['nullable', 'max:0'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
        ];

        if (! $request->user()) {
            $rules['guest_name'] = ['required', 'string', 'max:100'];
            $rules['guest_email'] = ['required', 'email', 'max:255'];
        }

        $validated = $request->validate($rules);

        if (! $request->user()?->is_admin && $spamFilter->isSpam(
            $validated['body'],
            $validated['guest_name'] ?? null,
            $validated['guest_email'] ?? null,
        )) {
            throw ValidationException::withMessages([
                'body' => 'Yorumunuz spam olarak algılandı.',
            ]);
        }

        $parent = null;

        if (! empty($validated['parent_id'])) {
            $parent = Comment::query()
                ->whereKey($validated['parent_id'])
                ->where('post_id', $post->id)
                ->firstOrFail();

            if ($parent->parent_id) {
                throw ValidationException::withMessages([
                    'body' => 'Yanıta tekrar yanıt verilemez.',
                ]);
            }
        }

        $comment = $post->comments()->create([
            'body' => $validated['body'],
            'parent_id' => $parent?->id,
            'user_id' => $request->user()?->id,
            'guest_name' => $validated['guest_name'] ?? null,
            'guest_email' => $validated['guest_email'] ?? null,
            'is_approved' => (bool) $request->user()?->is_admin,
        ]);

        $comment->loadMissing('post', 'parent.user');
        $metrics->increment($post, 'new_comments');

        if ($comment->is_approved && $parent?->user && $parent->user_id !== $comment->user_id) {
            $parent->user->notify(new CommentReplyNotification($comment));
        }

        $message = $request->user()?->is_admin
            ? 'Yorumunuz eklendi.'
            : 'Yorumunuz onay bekliyor.';

        return back()->with('message', $message);
    }
}
