<?php

namespace App\Services;

use App\Contracts\NewsletterSync;
use App\Models\NewsletterSubscription;

class NullNewsletterSync implements NewsletterSync
{
    public function sync(NewsletterSubscription $subscription): void
    {
        // Intentionally left blank until a provider integration is configured.
    }
}
