<?php

if (!function_exists('runBackgroundJob')) {
    function runBackgroundJob($className, $method, $priority = 1, $delay = 0, $retryAttempts = 3, ...$parameters)
    {
        require_once app_path('JobQueue/JobQueueManager.php');
        addJobToQueue($className, $method, $priority, $delay, $retryAttempts, ...$parameters);
        executeJobs();
    }
}
