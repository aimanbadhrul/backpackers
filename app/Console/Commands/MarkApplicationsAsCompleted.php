<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;

class MarkApplicationsAsCompleted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'applications:mark-completed';

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
        $completedEvents = Event::where('status', 'completed')->get();

        foreach ($completedEvents as $event) {
            $event->applications()
                ->whereIn('status', ['approved', 'confirmed'])
                ->update(['status' => 'completed']);
        }

        $this->info('Applications for completed events updated.');
    }
}
