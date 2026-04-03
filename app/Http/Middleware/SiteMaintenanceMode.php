<?php

namespace App\Http\Middleware;

use App\Support\SiteSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SiteMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = app(SiteSettings::class)->current();

        if (! $settings->maintenance_enabled) {
            return $next($request);
        }

        if ($request->user()?->is_admin) {
            return $next($request);
        }

        if ($this->isAllowedRoute($request)) {
            return $next($request);
        }

        return response()
            ->view('maintenance', ['site' => $settings], 503)
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    protected function isAllowedRoute(Request $request): bool
    {
        $name = $request->route()?->getName();

        if (! $name) {
            return false;
        }

        return in_array($name, [
            'login',
            'register',
            'password.request',
            'password.email',
            'password.reset',
            'password.store',
            'verification.notice',
            'verification.verify',
            'verification.send',
            'logout',
        ], true);
    }
}
