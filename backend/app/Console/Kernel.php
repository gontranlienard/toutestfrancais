<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ScrapeMotoSites;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        ScrapeMotoSites::class,
    ];

    protected function schedule($schedule)
    {
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
