<?php

namespace App\Console\Jobs;

class ErrorJob
{
    public function handle()
    {
        throw new \Exception("Simulated error in ErrorJob");
    }
}
