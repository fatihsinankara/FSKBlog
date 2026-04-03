<?php

namespace App\Support;

class SnippetSanitizer
{
    public static function isValid(?string $value, array $allowedTags, array $blockedTags = []): bool
    {
        if (blank($value)) {
            return true;
        }

        if (preg_match('/<\?(php|=)?/i', $value) === 1) {
            return false;
        }

        $blockedTags = collect($blockedTags)
            ->merge(['html', 'head', 'body', 'title', 'base'])
            ->map(fn (string $tag) => strtolower($tag))
            ->all();

        preg_match_all('/<\/?\s*([a-zA-Z0-9:-]+)/', $value, $matches);

        foreach ($matches[1] ?? [] as $tag) {
            $normalized = strtolower($tag);

            if (in_array($normalized, $blockedTags, true) || ! in_array($normalized, $allowedTags, true)) {
                return false;
            }
        }

        return true;
    }
}
