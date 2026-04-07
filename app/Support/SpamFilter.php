<?php

namespace App\Support;

class SpamFilter
{
    protected array $blockedPatterns = [
        // Yaygın spam kalıpları
        'casino', 'poker', 'viagra', 'cialis', 'buy cheap', 'free money',
        'click here', 'subscribe now', 'act now', 'limited time',
        'earn money', 'make money online', 'work from home',
        'lottery', 'winner', 'congratulations you',
        'crypto', 'bitcoin investment', 'forex trading',
        'sex', 'porn', 'xxx', 'adult',
    ];

    protected int $maxLinks = 2;

    public function isSpam(string $body, ?string $guestName = null, ?string $guestEmail = null): bool
    {
        $lowerBody = mb_strtolower($body);

        if ($this->hasTooManyLinks($lowerBody)) {
            return true;
        }

        if ($this->containsBlockedPatterns($lowerBody)) {
            return true;
        }

        if ($this->isRepeatedCharacters($body)) {
            return true;
        }

        if ($guestName && $this->hasLinksInName($guestName)) {
            return true;
        }

        if ($guestEmail && $this->isDisposableEmail($guestEmail)) {
            return true;
        }

        return false;
    }

    protected function hasTooManyLinks(string $body): bool
    {
        $linkCount = preg_match_all('/(https?:\/\/|www\.)/i', $body);

        return $linkCount > $this->maxLinks;
    }

    protected function containsBlockedPatterns(string $body): bool
    {
        foreach ($this->blockedPatterns as $pattern) {
            if (str_contains($body, $pattern)) {
                return true;
            }
        }

        return false;
    }

    protected function isRepeatedCharacters(string $body): bool
    {
        $cleaned = preg_replace('/\s+/', '', $body);

        if (mb_strlen($cleaned) < 10) {
            return false;
        }

        return (bool) preg_match('/(.)\1{9,}/', $cleaned);
    }

    protected function hasLinksInName(string $name): bool
    {
        return (bool) preg_match('/(https?:\/\/|www\.|\.\w{2,4}\/)/i', $name);
    }

    protected function isDisposableEmail(string $email): bool
    {
        $disposableDomains = [
            'tempmail.com', 'throwaway.email', 'guerrillamail.com',
            'mailinator.com', 'yopmail.com', 'sharklasers.com',
            'guerrillamailblock.com', 'grr.la', 'dispostable.com',
            'trashmail.com', 'fakeinbox.com', 'tempail.com',
            'temp-mail.org', 'minutemail.com', 'maildrop.cc',
        ];

        $domain = mb_strtolower(explode('@', $email)[1] ?? '');

        return in_array($domain, $disposableDomains, true);
    }
}
