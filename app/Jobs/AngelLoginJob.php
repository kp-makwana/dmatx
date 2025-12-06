<?php

namespace App\Jobs;

use App\Models\V1\Account;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AngelLoginJob implements ShouldQueue
{
    use Queueable;

    protected Account $account;

    /**
     * Create a new job instance.
     */
    public function __construct($account)
    {
        $this->account = $account;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
    }
}
