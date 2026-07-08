<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use Throwable;

class SnippetSanitizer
{
    public static function isValidHead(?string $value): bool
    {
        return self::isValid($value, ['meta', 'link', 'script', 'noscript']);
    }

    public static function isValidBodyEnd(?string $value): bool
    {
        return self::isValid($value, ['script', 'noscript', 'iframe', 'div']);
    }

    public static function isValid(?string $value, array $allowedTags, array $blockedTags = []): bool
    {
        if (blank($value)) {
            return true;
        }

        if (preg_match('/<\?(php|=)?/i', $value) === 1) {
            return false;
        }

        $allowedTags = collect($allowedTags)
            ->map(fn (string $tag) => strtolower($tag))
            ->all();

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

        try {
            $dom = new DOMDocument('1.0', 'UTF-8');
            $previous = libxml_use_internal_errors(true);
            $loaded = $dom->loadHTML(
                '<?xml encoding="utf-8" ?><html><body>'.$value.'</body></html>',
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
            );
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        } catch (Throwable) {
            return false;
        }

        if (! $loaded) {
            return false;
        }

        $body = $dom->getElementsByTagName('body')->item(0);

        if (! $body) {
            return false;
        }

        foreach ($body->childNodes as $node) {
            if (! self::isValidNode($node, $allowedTags, $blockedTags)) {
                return false;
            }
        }

        return true;
    }

    protected static function isValidNode(DOMNode $node, array $allowedTags, array $blockedTags): bool
    {
        if (in_array($node->nodeType, [XML_TEXT_NODE, XML_CDATA_SECTION_NODE, XML_COMMENT_NODE], true)) {
            return true;
        }

        if (! $node instanceof DOMElement) {
            return false;
        }

        $tag = strtolower($node->tagName);

        if (in_array($tag, $blockedTags, true) || ! in_array($tag, $allowedTags, true)) {
            return false;
        }

        if (! self::hasValidAttributes($node, $tag)) {
            return false;
        }

        foreach ($node->childNodes as $child) {
            if (! self::isValidNode($child, $allowedTags, $blockedTags)) {
                return false;
            }
        }

        return true;
    }

    protected static function hasValidAttributes(DOMElement $element, string $tag): bool
    {
        if ($tag === 'script' && ! $element->hasAttribute('src')) {
            return false;
        }

        if ($tag === 'iframe' && ! $element->hasAttribute('src')) {
            return false;
        }

        foreach ($element->attributes as $attribute) {
            $name = strtolower($attribute->nodeName);
            $value = trim($attribute->nodeValue ?? '');

            if (str_starts_with($name, 'on')) {
                return false;
            }

            if (! self::isAllowedAttribute($tag, $name)) {
                return false;
            }

            if (in_array($name, ['src', 'href'], true) && ! self::isSafeUrl($value)) {
                return false;
            }

            if (in_array($name, ['imagesrcset'], true) && ! self::isSafeSrcSet($value)) {
                return false;
            }
        }

        return true;
    }

    protected static function isAllowedAttribute(string $tag, string $attribute): bool
    {
        if (
            in_array($attribute, ['id', 'class', 'title'], true)
            || str_starts_with($attribute, 'data-')
            || str_starts_with($attribute, 'aria-')
        ) {
            return true;
        }

        return in_array($attribute, match ($tag) {
            'meta' => ['name', 'content', 'property', 'charset'],
            'link' => ['rel', 'href', 'as', 'type', 'media', 'crossorigin', 'integrity', 'referrerpolicy', 'fetchpriority', 'imagesizes', 'imagesrcset', 'sizes'],
            'script' => ['src', 'async', 'defer', 'type', 'crossorigin', 'integrity', 'referrerpolicy', 'fetchpriority', 'nomodule', 'nonce'],
            'style' => ['media', 'nonce'],
            'iframe' => ['src', 'width', 'height', 'loading', 'referrerpolicy', 'allow', 'allowfullscreen', 'title', 'sandbox'],
            'div', 'noscript' => [],
            default => [],
        }, true);
    }

    protected static function isSafeUrl(string $value): bool
    {
        if ($value === '') {
            return true;
        }

        if (str_starts_with($value, '//')) {
            return false;
        }

        if (
            str_starts_with($value, '/')
            || str_starts_with($value, '#')
            || str_starts_with($value, '?')
        ) {
            return true;
        }

        $parts = parse_url($value);

        if (($parts['scheme'] ?? null) !== 'https' || empty($parts['host'])) {
            return false;
        }

        $host = strtolower($parts['host']);
        $allowedHosts = config('security.snippet_allowed_hosts', []);

        return in_array($host, $allowedHosts, true);
    }

    protected static function isSafeSrcSet(string $value): bool
    {
        if ($value === '') {
            return true;
        }

        $sources = array_filter(array_map('trim', explode(',', $value)));

        foreach ($sources as $source) {
            $url = strtok($source, ' ');

            if (! self::isSafeUrl($url ?: '')) {
                return false;
            }
        }

        return true;
    }
}
