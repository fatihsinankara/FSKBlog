<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\UserContentFollow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function toggleCategory(Request $request, Category $category): RedirectResponse
    {
        return $this->toggle($request, $category, 'Kategori takibi güncellendi.');
    }

    public function toggleCollection(Request $request, Collection $collection): RedirectResponse
    {
        abort_unless($collection->status === 'published', 404);

        return $this->toggle($request, $collection, 'Seri takibi güncellendi.');
    }

    protected function toggle(Request $request, mixed $followable, string $message): RedirectResponse
    {
        $follow = UserContentFollow::query()
            ->where('user_id', $request->user()->id)
            ->where('followable_type', $followable::class)
            ->where('followable_id', $followable->id)
            ->first();

        if ($follow) {
            $follow->delete();

            return back()->with('message', 'Takipten çıkarıldı.');
        }

        UserContentFollow::create([
            'user_id' => $request->user()->id,
            'followable_type' => $followable::class,
            'followable_id' => $followable->id,
        ]);

        return back()->with('message', $message);
    }
}
