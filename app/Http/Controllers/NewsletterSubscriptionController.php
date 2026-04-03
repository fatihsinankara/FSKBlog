<?php

namespace App\Http\Controllers;

use App\Contracts\NewsletterSync;
use App\Models\NewsletterSubscription;
use App\Support\SiteSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterSubscriptionController extends Controller
{
    public function confirm(string $token, SiteSettings $siteSettings): View
    {
        $subscription = NewsletterSubscription::query()
            ->where('unsubscribe_token', $token)
            ->firstOrFail();

        return view('newsletter.unsubscribe', [
            'site' => $siteSettings->current(),
            'subscription' => $subscription,
        ]);
    }

    public function store(Request $request, NewsletterSync $sync): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'name' => ['nullable', 'string', 'max:100'],
            'frequency' => ['nullable', 'in:instant,weekly'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ]);

        $subscription = NewsletterSubscription::query()->firstOrNew([
            'email' => $validated['email'],
        ]);

        $subscription->fill([
            'name' => $validated['name'] ?? $subscription->name,
            'status' => 'subscribed',
            'frequency' => $validated['frequency'] ?? 'instant',
            'categories' => collect($validated['categories'] ?? [])->map(fn ($id) => (int) $id)->unique()->values()->all(),
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
            'unsubscribe_token' => $subscription->unsubscribe_token ?: Str::random(40),
        ]);
        $subscription->save();

        $sync->sync($subscription);

        return back()->with('message', 'Bülten aboneliğin güncellendi.');
    }

    public function destroy(string $token, NewsletterSync $sync): RedirectResponse
    {
        $subscription = NewsletterSubscription::query()
            ->where('unsubscribe_token', $token)
            ->firstOrFail();

        if ($subscription->status !== 'unsubscribed') {
            $subscription->update([
                'status' => 'unsubscribed',
                'unsubscribed_at' => now(),
            ]);

            $sync->sync($subscription);

            return redirect()->route('home')->with('message', 'Bülten aboneliğin sonlandırıldı.');
        }

        return redirect()->route('home')->with('message', 'Bülten aboneliğin zaten sonlandırılmıştı.');
    }
}
