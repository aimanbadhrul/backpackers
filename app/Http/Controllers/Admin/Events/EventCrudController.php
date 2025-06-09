<?php

namespace App\Http\Controllers\Admin\Events;

use App\Models\User;
use App\Models\Event;
use Prologue\Alerts\Facades\Alert;
use App\Http\Requests\EventRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class EventCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EventCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        $routePrefix = config('backpack.base.route_prefix');
        CRUD::setModel(Event::class);
        CRUD::setRoute($routePrefix . '/event');
        CRUD::setEntityNameStrings('event', 'events');

        CRUD::addClause('where', 'status', '!=', 'completed');
    }

    protected function setupListOperation()
    {
        $user = backpack_user();
        $this->crud->query->withCount('applications'); // Load the count of applications

        if (!$user->can('approve events')) {
            CRUD::addClause('where', 'created_by', backpack_user()->id);
        }

        //Filter out Drafts not created by the user
        if (!$user->hasRole('Superadmin')) {
            CRUD::addClause('where', function ($query) {
                $userId = backpack_user()->id;
                $query->where(function ($q) use ($userId) {
                    $q->where('status', '!=', 'draft')
                      ->orWhere('created_by', $userId);
                });
            });
        }

        CRUD::addColumn([
            'name'  => 'title',  // Relationship method
            'label' => 'Event Title',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'createdBy.name',  // Relationship method
            'label' => 'Created By',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'location',
            'label' => 'Location',
            'type' => 'text',
        ]);
        
        CRUD::addColumn([
            'name' => 'start_date',
            'label' => 'Start Date',
            'type' => 'date',
        ]);
    
        CRUD::addColumn([
            'name' => 'end_date',
            'label' => 'End Date',
            'type' => 'date',
        ]);

        // CRUD::addColumn([
        //     'name' => 'applications_count',
        //     'label' => 'Applicants',
        //     'type' => 'number',
        //     'default' => 0,
        //     'wrapper' => [
        //         'class' => 'd-flex justify-content-end pe-5', // Align text to the right
        //     ],
        // ]);

        CRUD::addColumn([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'custom_html',
            'value' => fn($entry) => $entry->getStatusBadge(),
            'escaped' => false, // Allows HTML rendering
        ]);

        // if (!backpack_user()->hasRole('Superadmin')) {
            // Remove all default action buttons
            CRUD::removeButton('update');
            CRUD::removeButton('delete');
            // CRUD::removeButton('show');
        // }
    }

    protected function setupCreateOperation()
    {
        $user = backpack_user();
        $event = $this->crud->getCurrentEntry();
        CRUD::setValidation(EventRequest::class);
        CRUD::setHeading('Create Event');
        CRUD::setSubheading('Fill in event details & submit for approval');

        CRUD::field('title')
        ->type('text')
        ->label('Event Title');

        CRUD::field('description')
        ->type('textarea')
        ->label('Description');

        CRUD::field('location')
        ->type('text')
        ->label('Location');

        CRUD::field('start_date')
        ->label('Start Date')
        ->type('date')
        ->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('end_date')
        ->label('End Date')
        ->type('date')
        ->wrapper(['class' => 'form-group col-md-3']);

        // CRUD::field('row_start')->type('custom_html')->value('<div class="row">');
        CRUD::field('max_participants')
        ->type('number')
        ->label('Maximum Participants')
        ->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('cost')
        ->type('number')
        ->label('Cost')
        ->prefix('RM ')
        ->wrapper(['class' => 'form-group col-md-3']);

        if ($user->hasRole('Superadmin')) {
            CRUD::addField([
                'name' => 'status',
                'label' => 'Status',
                'type' => 'select_from_array',
                'options' => Event::getStatuses(),
                'allows_null' => false, // Ensures a selection is made
                'default' => 'draft', // Default to Draft
            ]);
        }

        CRUD::addSaveAction([
            'name' => 'submit_for_approval',
            'redirect' => fn($crud, $request, $itemId) => url('admin/event/' . $itemId . '/submit'),
            'button_text' => 'Submit for Approval',
        ]);
    }

    protected function setupUpdateOperation()
    {
        $user = backpack_user();
        $event = $this->crud->getCurrentEntry();
        $this->setupCreateOperation();
        CRUD::setHeading('Edit Event');

        if ($user->hasRole('Superadmin')) {
            CRUD::field('created_by')
            ->type('select')
            ->label('Created By')
            ->entity('createdBy')
            ->model(User::class)
            ->attribute('name');
        }

        if ($event->canSubmitForApproval()) {
            CRUD::addSaveAction([
                'name' => 'submit_for_approval',
                'redirect' => fn($crud, $request, $itemId) => url('admin/event/' . $itemId . '/submit'),
                'button_text' => 'Submit for Approval',
            ]);
        }
    }

    protected function setupShowOperation()
    {
        CRUD::removeButton('update');
        CRUD::removeButton('delete');
            // ----------- Details Tab -----------
        CRUD::addColumn([
            'name' => 'title',
            'label' => 'Event Title',
            'type' => 'text',
            'tab' => 'Details',
        ]);
        CRUD::addColumn([
            'name' => 'location',
            'label' => 'Location',
            'type' => 'text',
            'tab' => 'Details',
        ]);
        CRUD::addColumn([
            'name' => 'start_date',
            'label' => 'Start Date',
            'type' => 'date',
            'tab' => 'Details',
        ]);
        CRUD::addColumn([
            'name' => 'end_date',
            'label' => 'End Date',
            'type' => 'date',
            'tab' => 'Details',
        ]);
        CRUD::addColumn([
            'name' => 'cost',
            'label' => 'Cost (RM)',
            'type' => 'float',
            'tab' => 'Details',
        ]);

        $event = $this->crud->getCurrentEntry();

        CRUD::addColumn([
            'name'  => 'approved_participants',
            'label' => 'Participants',
            'type'  => 'custom_html',
            'tab'   => 'Participant List',
            'value' => $this->getApprovedParticipantsHtml($event),
        ]);  

        CRUD::addColumn([
            'name' => 'itinerary',
            'label' => 'Itinerary',
         'type' => 'custom_html',
            'value' => fn ($entry) 
            => $entry->itinerary ?: '<em>No itinerary available.</em>',
            'escaped' => false,
            'tab' => 'Itinerary',
        ]);

        CRUD::addColumn([
            'name' => 'checklist',
            'label' => 'Item Checklist',
            'type' => 'custom_html',
            'value' => fn ($entry) 
            => $entry->checklist ?: '<em>No checklist provided.</em>',
            'escaped' => false,
            'tab' => 'Item Checklist',
        ]);

        CRUD::addColumn([
            'name' => 'additional_info',
            'label' => 'Additional Info',
            'type' => 'custom_html',
            'value' => fn ($entry) 
            => $entry->additional_info ?: '<em>No additional info.</em>',
            'escaped' => false,
            'tab' => 'Additional Information',
        ]);

        //Timestamp Tab (Spatie Activitylog)

        $eventId = $this->crud->getCurrentEntryId();
        $event = Event::find($eventId);
        $labelMap = [
            'title' => 'Title',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'status' => 'Status',
            'created_by' => 'Created By',
            'location' => 'Location',
            'max_participants' => 'Max Participants',
            'cost' => 'Cost',
            'itinerary' => 'Itinerary',
            'item_checklist' => 'Item Checklist',
            'additional_info' => 'Additional Info'
        ];
        
        $activities = $event->activities()->latest()->get();
        
        $html = '';
        
        if ($activities->isEmpty()) {
            $html .= '<p><em></em>No activity log available.</em></p>';
        } else {
            $html .= '
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Caused By</th>
                                <th>Changes</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($activities as $activity) {
                $causer = $activity->causer ? $activity->causer->name : 'System';
                $changes = '';

                if ($activity->properties && $activity->properties->has('attributes')) {
                    $attributes = $activity->properties['attributes'];

                    // Define the custom order
                    $customOrder = [
                        'status',
                        'title',
                        'start_date',
                        'end_date',
                        'location',
                        'max_participants',
                        'cost',
                        'itinerary',
                        'item_checklist',
                        'additional_info',
                        'created_by',
                    ];

                    // Sort attributes based on the custom order
                    uksort($attributes, function ($a, $b) use ($customOrder) {
                        $posA = array_search($a, $customOrder);
                        $posB = array_search($b, $customOrder);
                        return $posA <=> $posB;
                    });

                    foreach ($attributes as $key => $value) {
                        $label = $labelMap[$key] ?? ucfirst(str_replace('_', ' ', $key));

                        // Format value based on key
                        if (in_array($key, ['start_date', 'end_date']) && strtotime($value)) {
                            $formattedValue = \Carbon\Carbon::parse($value)->format('d M Y');
                        } elseif ($key === 'created_by') {
                            $user = User::find($value);
                            $formattedValue = $user ? $user->name : "User ID: {$value}";
                        } else {
                            $formattedValue = e($value);
                        }

                        $changes .= "<strong>{$label}</strong>: {$formattedValue}<br>";
                    }
                }


                $html .= '
                            <tr>
                                <td>' . $activity->created_at->format('Y-m-d H:i') . '</td>
                                <td>' . e($activity->description) . '</td>
                                <td>' . e($causer) . '</td>
                                <td>' . $changes . '</td>
                            </tr>';
            }

            $html .= '</tbody></table></div>';
        }
        
        CRUD::addColumn([
            'name'  => 'activity_log',
            'label' => '',
            'type'  => 'custom_html',
            'tab'   => 'Timestamp (Log)',
            'value' => $html,
        ]);
    }

    private function getApprovedParticipantsHtml($event)
    {
        $approvedParticipants = $event->approvedParticipants;
    
        $html = '';
    
        if ($approvedParticipants->isEmpty()) {
            $html .= '<p><em>No approved participants yet.</em></p>';
        } else {
            $html .= '
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($approvedParticipants as $index => $participant) {
                $html .= '
                            <tr>
                                <td>' . ($index + 1) . '</td>
                                <td>' . e($participant->full_name) . '</td>
                                <td>' . e($participant->email) . '</td>
                                <td>' . e($participant->phone) . '</td>
                            </tr>';
            }
            $html .= '
                        </tbody>
                    </table>
                </div>';
        }
    
        return $html;
    }   

    public function submit($id)
    {
        $user = backpack_user();
        $event = Event::findOrFail($id);
    
        if ($user->hasRole('Superadmin') || $event->created_by === $user->id) {
            // activity()
            // ->performedOn($event)  // Associate activity with the event model
            // ->causedBy(backpack_user())  // Associate the logged action with the user
            // ->withProperties(['status' => 'submitted'])
            // ->log('Event submitted for approval');

            $event->status = 'submitted';
            $event->submitted_at = now();
            $event->save();

            Alert::success('Event submitted for approval.')->flash();
            return redirect('/admin/submitted-event');
        }
    
        abort(403, 'You are not allowed to submit this event.');
    }
    
}
