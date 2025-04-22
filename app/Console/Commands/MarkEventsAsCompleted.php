<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;

class MarkEventsAsCompleted extends Command
{
    // This defines the name of the Artisan command
    protected $signature = 'events:mark-completed';

    protected $description = 'Mark approved events as completed if their end date has passed';

    public function handle()
    {
        // Handle newly completed events
        $completedEvents = Event::where('status', 'approved')
            ->where('end_date', '<', now())
            ->get();
    
        foreach ($completedEvents as $event) {
            $event->update(['status' => 'completed']);
    
            $event->applications()
                ->whereIn('status', ['approved', 'confirmed'])
                ->update(['status' => 'completed']);
        }
    
        // Ensure already-completed events also have correct application status
        $alreadyCompletedEvents = Event::where('status', 'completed')->get();
    
        foreach ($alreadyCompletedEvents as $event) {
            $event->applications()
                ->whereIn('status', ['approved', 'confirmed'])
                ->update(['status' => 'completed']);
        }
    
        $this->info('All completed events and applications updated.');
    }
    
}
