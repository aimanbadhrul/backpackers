<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EventRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Event;

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

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->query->withCount('applications'); // Load the count of applications

        if (backpack_user()->hasRole('Event Leader')) {
            CRUD::addClause('where', 'created_by', backpack_user()->id);
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

        // CRUD::addButtonFromModelFunction('line', 'submit_for_approval', 'submitButton', 'end');
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
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

        CRUD::field('row_start')->type('custom_html')->value('<div class="row">');
        CRUD::field('max_participants')
        ->type('number')
        ->label('Maximum Participants');

        CRUD::field('cost')
        ->type('number')
        ->label('Cost')
        ->prefix('RM ');

        if (backpack_user() && backpack_user()->can('approve events')) {
            CRUD::addField([
                'name' => 'status',
                'label' => 'Status',
                'type' => 'select_from_array',
                'options' => Event::getStatuses(),
                'allows_null' => false, // Ensures a selection is made
                'default' => 'draft', // Default to Draft
            ]);
        }
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        CRUD::setHeading('Edit Event');

        $event = $this->crud->getCurrentEntry();

        if (backpack_user()->hasRole('Event Leader')) {
            if (!$event->canEdit()) {
                abort(403, 'You are not allowed to edit this event.');
            }
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
            'label' => 'Cost',
            'type' => 'number',
            'tab' => 'Details',
        ]);

        $event = $this->crud->getCurrentEntry();

        CRUD::addColumn([
            'name'  => 'approved_participants',
            'label' => '',
            'type'  => 'custom_html',
            'tab'   => 'Participant List',
            'value' => $this->getApprovedParticipantsHtml($event),
        ]);  

        CRUD::addColumn([
            'name' => 'itinerary',
            'label' => 'Itinerary',
         'type' => 'custom_html',
            'value' => function ($entry) {
                return $entry->itinerary ?: '<em>No itinerary available.</em>';
            },
            'escaped' => false,
            'tab' => 'Itinerary',
        ]);

        CRUD::addColumn([
            'name' => 'checklist',
            'label' => 'Item Checklist',
            'type' => 'custom_html',
            'value' => function ($entry) {
                return $entry->checklist ?: '<em>No checklist provided.</em>';
            },
            'escaped' => false,
            'tab' => 'Item Checklist',
        ]);

        CRUD::addColumn([
            'name' => 'additional_info',
            'label' => 'Additional Info',
            'type' => 'custom_html',
            'value' => function ($entry) {
                return $entry->additional_info ?: '<em>No additional info.</em>';
            },
            'escaped' => false,
            'tab' => 'Additional Information',
        ]);
    }

    private function getApprovedParticipantsHtml($event)
    {
        $approvedParticipants = $event->approvedParticipants;
    
        $html = '<h5><strong>Participants</strong></h5>';
    
        if ($approvedParticipants->isEmpty()) {
            $html .= '<p>No approved participants yet.</p>';
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
}
