<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    public function store(Request $request, Post $post): RedirectResponse
    {
        if (filled($request->input('website'))) {
            throw ValidationException::withMessages([
                'body' => 'Yorum gönderilemedi.',
            ]);
        }

        $rules = [
            'body' => ['required', 'string', 'min:3', 'max:2000'],
            'website' => ['nullable', 'max:0'],
        ];

        if (!$request->user()) {
            $rules['guest_name']  = ['required', 'string', 'max:100'];
            $rules['guest_email'] = ['required', 'email', 'max:255'];
        }

        $validated = $request->validate($rules);

        $post->comments()->create([
            'body'        => $validated['body'],
            'user_id'     => $request->user()?->id,
            'guest_name'  => $validated['guest_name'] ?? null,
            'guest_email' => $validated['guest_email'] ?? null,
            'is_approved' => (bool) $request->user()?->is_admin,
        ]);

        $message = $request->user()?->is_admin
            ? 'Yorumunuz eklendi.'
            : 'Yorumunuz onay bekliyor.';

        return back()->with('message', $message);
    }
}
