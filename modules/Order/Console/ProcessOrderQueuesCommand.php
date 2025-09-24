<?php

declare(strict_types=1);

namespace Modules\Order\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ProcessOrderQueuesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'order:process-queues 
                            {--timeout=60 : The timeout for the queue worker}
                            {--sleep=3 : The sleep time when no jobs are available}
                            {--tries=3 : Number of attempts to process a job}';

    /**
     * The console command description.
     */
    protected $description = 'Process order-related queue jobs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting order queue processing...');

        // Process different queues with different priorities
        $queues = [
            'emails' => 'High priority - Email notifications',
            'inventory' => 'Medium priority - Inventory updates',
            'alerts' => 'Medium priority - Stock alerts',
            'events' => 'Low priority - Event processing'
        ];

        foreach ($queues as $queue => $description) {
            $this->line("Processing queue: {$queue} - {$description}");
        }

        // Start queue worker for order-related queues
        $exitCode = Artisan::call('queue:work', [
            '--queue' => implode(',', array_keys($queues)),
            '--timeout' => $this->option('timeout'),
            '--sleep' => $this->option('sleep'),
            '--tries' => $this->option('tries'),
            '--verbose' => true,
        ]);

        if ($exitCode === 0) {
            $this->info('Order queue processing completed successfully.');
        } else {
            $this->error('Order queue processing failed.');
        }

        return $exitCode;
    }
}
