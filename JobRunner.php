<?php

require __DIR__ . '/vendor/autoload.php';

$allowedClasses = [
    \App\Console\Jobs\ExampleJob::class,
    \App\Console\Jobs\ErrorJob::class,
    \App\Console\Jobs\DelayedJob::class,
];

/**
 * Sanitize input to avoid unwanted characters.
 */
function sanitizeInput($input)
{
    $sanitizedInput = filter_var($input, FILTER_SANITIZE_STRING);
    return preg_replace('/[^A-Za-z0-9_\\\\]/', '', $sanitizedInput);
}

/**
 * Log job status to storage/logs.
 */
function logJobStatus($className, $method, $status, $errorMessage = null)
{
    $logFile = __DIR__ . '/storage/logs/background_jobs.log';
    $errorFile = __DIR__ . '/storage/logs/background_jobs_errors.log';

    $logMessage = date('Y-m-d H:i:s') . " Job: {$className}@{$method} | Status: {$status}";
    if ($errorMessage) {
        $logMessage .= " | Error: {$errorMessage}";
    }

    file_put_contents($status === 'failed' ? $errorFile : $logFile, $logMessage . PHP_EOL, FILE_APPEND);
}

$className = isset($argv[1]) ? sanitizeInput($argv[1]) : null;
$method = isset($argv[2]) ? sanitizeInput($argv[2]) : null;
$delay = isset($argv[3]) ? (int) $argv[3] : 0;
$parameters = array_slice($argv, 4);

if (!$className || !$method || !in_array($className, $allowedClasses, true)) {
    logJobStatus($className, $method, 'failed', 'Unauthorized class or method');
    exit(1);
}

// Apply delay if specified
if ($delay > 0) {
    sleep($delay);
}

try {
    $attempts = 0;
    $maxRetries = 3; // Max retries before failure
    $jobSucceeded = false;

    do {
        $classInstance = new $className();
        if (!method_exists($classInstance, $method)) {
            throw new Exception("Method {$method} does not exist in {$className}.");
        }

        logJobStatus($className, $method, 'running');
        call_user_func_array([$classInstance, $method], $parameters);
        logJobStatus($className, $method, 'completed');
        $jobSucceeded = true;

    } while (!$jobSucceeded && $attempts++ < $maxRetries);

    if (!$jobSucceeded) {
        throw new Exception("Job failed after {$maxRetries} attempts.");
    }
} catch (Exception $e) {
    logJobStatus($className, $method, 'failed', $e->getMessage());
    exit(1);
}
