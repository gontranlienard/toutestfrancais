<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanVisitorLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-visitor-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table('visitor_logs')
    ->where('created_at', '<', now()->subDays(60))
    ->delete();
    }
}
