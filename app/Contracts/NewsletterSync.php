<?php

namespace App\Contracts;

use App\Models\NewsletterSubscription;

interface NewsletterSync
{
    public function sync(NewsletterSubscription $subscription): void;
}
