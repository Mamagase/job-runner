# Background Job Runner

This project provides a background job runner system, allowing jobs to be queued, executed with retries, and logged for status and errors. It includes several components for managing job execution, including job classes, a job queue manager, and a helper function for adding jobs to the queue.

## Overview

This system includes:

- **Job Queue**: Manages the order and priority of background jobs.
- **Job Execution**: Executes jobs in the background using PHP’s command-line interface.
- **Retry Logic**: Automatically retries failed jobs based on configured retry attempts.
- **Job Delays**: Adds delays between job executions if required.
- **Job Logging**: Logs job statuses and errors for monitoring and debugging.

## Key Components

- **Job Classes**: Background jobs with a `handle` method that contains the job logic.
- **Job Queue Manager**: Manages the queue and prioritization of jobs.
- **Job Helper**: Provides a simple interface to add jobs to the queue and execute them.
- **Job Runner**: A standalone script (`JobRunner.php`) that runs jobs from the queue.
- **Log Files**: Logs job statuses and errors into files for tracking.

## Installation

### Prerequisites

- **PHP 8.2.4 or higher**: Ensure PHP is installed and meets the minimum version requirement.
- **Composer**: Used for managing dependencies.
- **Command-Line Interface (CLI)**: Access to a CLI to run PHP scripts.

### Steps to Install

1.  **Clone the Repository**
    ```bash
    git clone <repository-url>

2.  **Install Dependencies**
    ```bash
    composer install

3.  **Generate Application Key**
    - This step is required for Laravel projects to ensure your application is properly secured.
    ```bash
    php artisan key:generate

4. **Start the Development Server**
    - This will start server at http://localhost:8000 by default
    ```bash
    php artisan serve

# File Structure

```
project_root/
├── app/
│   ├── Console/
│   │   └── Jobs/
│   │       ├── ExampleJob.php          # Example job class
│   │       ├── ErrorJob.php            # Error-generating job class
│   │       └── DelayedJob.php          # Job with delay for testing
│   ├── Helpers/
│   │   └── JobHelper.php               # Helper function for job validation
│   └── JobQueue/
│       └── JobQueueManager.php         # Manages job queue and priorities
├── JobRunner.php                       # Standalone job execution script
└── storage/
    └── logs/
        ├── background_jobs.log         # Log for job statuses
        └── background_jobs_errors.log  # Log for job errors
```

# Usage

## How `runBackgroundJob` Works

### Function Breakdown

#### Parameters
- **`$className`**: The fully qualified class name of the job (e.g., `\App\Console\Jobs\ExampleJob`).
- **`$method`**: The method to execute within the job class (e.g., `handle`).
- **`$priority`**: The priority of the job (higher values indicate higher priority).
- **`$delay`**: The number of seconds to wait before executing the job.
- **`$retryAttempts`**: The number of times the job should be retried in case of failure.
- **`$parameters`**: Any additional parameters to pass to the job method.

#### Flow

1. **Job Addition**: The job is added to the queue using `addJobToQueue`.
2. **Queue Sorting**: Jobs are sorted based on priority, ensuring higher priority jobs are executed first.
3. **Job Execution**: 
   - The job is executed via the `JobRunner.php` script.
   - If the job fails, it is automatically retried up to the specified `retryAttempts`.

    ## How to run a background job using the `runBackgroundJob`
    ```php
    runBackgroundJob(\App\Console\Jobs\ExampleJob::class, 'handle', 1, 0, 3, 'param1', 'param2');
    runBackgroundJob(\App\Console\Jobs\ErrorJob::class, 'handle', 2, 5, 2);

## Explanation of the job 
    
#### `ExampleJob`

- **Priority**: `1`  
  - This job has a lower priority than `ErrorJob`.
- **Retry Attempts**: `3`  
  - If the job fails, it will be retried up to three times.
- **Delay**: `0` seconds  
  - The job will execute immediately with no delay.
- **Parameters**: `"param1"` and `"param2"`  
  - These values are passed to the job's `handle` method.

#### `ErrorJob`

- **Priority**: `2`  
  - This job has a higher priority than `ExampleJob`, so it will be executed first.
- **Retry Attempts**: `2`  
  - If the job fails, it will be retried up to two times.
- **Delay**: `5` seconds  
  - This job will wait five seconds before execution.

## Logging Job Status

- **`storage/logs/background_jobs.log`**: Contains general logs for job statuses, such as when a job starts, completes, or retries.
- **`storage/logs/background_jobs_errors.log`**: Records error messages for jobs that fail, providing details for troubleshooting.

## Conclusion

The Background Job Runner offers a straightforward solution for handling asynchronous job execution. It supports key features like retry attempts, delays, and configurable priorities to ensure efficient job processing.

Using the `runBackgroundJob` helper function, jobs are added to the queue and executed through the `JobRunner.php` script. This setup enables flexible and reliable job management, making it easier to run background tasks in a controlled and efficient manner.

With built-in logging for job statuses and errors, the system provides visibility into job execution, helping with monitoring and debugging.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
