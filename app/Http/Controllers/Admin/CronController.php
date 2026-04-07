<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class CronController extends Controller
{
    public function index(Schedule $schedule): Response
    {
        $basePath = base_path();
        $phpBinary = PHP_BINARY;

        return Inertia::render('Admin/Cron/Index', [
            'cron_command' => "* * * * * cd {$basePath} && {$phpBinary} artisan schedule:run >> /dev/null 2>&1",
            'php_binary' => $phpBinary,
            'base_path' => $basePath,
            'server_info' => [
                'hostname' => gethostname(),
                'php_version' => PHP_VERSION,
                'os' => php_uname('s').' '.php_uname('r'),
                'server_time' => now()->toIso8601String(),
                'timezone' => config('app.timezone'),
            ],
            'schedule' => $this->getScheduledTasks($schedule),
            'last_run' => Cache::get('schedule:last_run'),
            'cron_active' => $this->isCronActive(),
        ]);
    }

    public function ping(): RedirectResponse
    {
        Cache::put('schedule:last_run', now()->toIso8601String(), now()->addDay());

        return back()->with('message', 'Cron durumu güncellendi.');
    }

    protected function getScheduledTasks(Schedule $schedule): array
    {
        $tasks = [];

        foreach ($schedule->events() as $event) {
            if ($event->command) {
                $command = str_replace([PHP_BINARY, "'".PHP_BINARY."'", base_path('artisan'), "'artisan'"], '', $event->command);
                $command = trim($command, " \t\n\r\0\x0B'\"");
            } else {
                $command = $event->description ?: 'Closure';
            }

            $tasks[] = [
                'expression' => $event->expression,
                'command' => $command,
                'next_due' => $event->nextRunDate()->format('d M Y H:i'),
            ];
        }

        return $tasks;
    }

    protected function isCronActive(): bool
    {
        $lastRun = Cache::get('schedule:last_run');

        if (! $lastRun) {
            return false;
        }

        return now()->diffInMinutes($lastRun) < 5;
    }
}
