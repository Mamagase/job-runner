<?php

$jobQueue = [];

/**
 * Add job to queue with retry attempts, priority, and delay.
 */
function addJobToQueue($className, $method, $priority = 1, $delay = 0, $retryAttempts = 3, ...$parameters)
{
    global $jobQueue;
    $jobQueue[] = [
        'className' => $className,
        'method' => $method,
        'priority' => $priority,
        'delay' => $delay,
        'retryAttempts' => $retryAttempts, // Store retry attempts
        'parameters' => $parameters,
    ];
    usort($jobQueue, fn($a, $b) => $b['priority'] <=> $a['priority']);
}

/**
 * Execute jobs by calling JobRunner.php as a standalone background process.
 */
function executeJobs()
{
    global $jobQueue;
    while (!empty($jobQueue)) {
        $job = array_shift($jobQueue);
        $attempts = 0;

        do {
            $cmd = "php " . __DIR__ . "/../../JobRunner.php " . escapeshellarg($job['className']) . " " .
            escapeshellarg($job['method']) . " " .
            $job['delay'] . " " .
            implode(" ", array_map("escapeshellarg", $job['parameters']));

            $status = exec($cmd . " > /dev/null &"); // Execute job

            // Check if the job succeeded or failed
            if ($status === 'failed' && $attempts < $job['retryAttempts']) {
                sleep($job['delay']); // Delay before retry
                $attempts++;
            } else {
                break;
            }
        } while ($attempts < $job['retryAttempts']);
    }
}
