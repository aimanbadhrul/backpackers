<?php

namespace App\Schedule;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Pennant\Feature;
use Illuminate\Support\ServiceProvider;

class Kernel extends ServiceProvider
{
    public function schedule(Schedule $schedule): void
    {
        // Schedule your command
        $schedule->command('events:mark-completed')->daily();
    }

    public function boot(): void
    {
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $this->schedule($schedule);
        });
    }
}
