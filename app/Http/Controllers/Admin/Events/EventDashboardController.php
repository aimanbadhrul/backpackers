<?php

namespace App\Http\Controllers\Admin\Events;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventDashboardController extends Controller
{
    public function show(Event $event)
    {
        // Ensure the user has permission to view the event dashboard
        if (!backpack_user()->can('view events')) {
            abort(403, 'Unauthorized access');
        }

        return view('admin.event_dashboard', compact('event'));
    }
}
