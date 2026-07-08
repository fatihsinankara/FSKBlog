<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Throwable;

class Turnstile
{
    public const FIELD = 'cf_turnstile_response';

    public function enabled(): bool
    {
        return filled($this->siteKey()) && filled($this->secretKey());
    }

    public function siteKey(): ?string
    {
        return config('services.turnstile.site_key');
    }

    public function publicConfig(): array
    {
        return [
            'enabled' => $this->enabled(),
            'site_key' => $this->enabled() ? $this->siteKey() : null,
        ];
    }

    public function validate(Request $request, string $errorField = self::FIELD): void
    {
        if (! $this->enabled()) {
            return;
        }

        $token = (string) $request->input(self::FIELD, '');

        if ($token === '') {
            $this->fail($errorField);
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post((string) config('services.turnstile.verify_url'), [
                    'secret' => $this->secretKey(),
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ]);
        } catch (Throwable) {
            $this->fail($errorField);
        }

        if (! ($response->json('success') === true)) {
            $this->fail($errorField);
        }
    }

    protected function secretKey(): ?string
    {
        return config('services.turnstile.secret_key');
    }

    protected function fail(string $field): never
    {
        throw ValidationException::withMessages([
            $field => 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.',
        ]);
    }
}
