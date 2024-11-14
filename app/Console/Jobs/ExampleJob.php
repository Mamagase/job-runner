<?php

namespace App\Console\Jobs;

class ExampleJob
{
    public function handle($param1 = null, $param2 = null)
    {
        echo "Running ExampleJob with parameters: $param1, $param2" . PHP_EOL;
    }
}
