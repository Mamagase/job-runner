<?php

namespace App\Console\Jobs;

class DelayedJob
{
    public function handle($delay)
    {
        echo "DelayedJob started. Sleeping for $delay seconds...\n";
        sleep($delay);
        echo "DelayedJob completed after $delay seconds\n";
    }
}
