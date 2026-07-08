<?php

namespace Tests;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'session.driver' => 'array',
            'services.turnstile.site_key' => null,
            'services.turnstile.secret_key' => null,
        ]);

        $this->withoutMiddleware(PreventRequestForgery::class);
    }
}
