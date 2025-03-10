<?php

namespace App\Http\Controllers\Admin;

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
        CRUD::setModel(\App\Models\Event::class);
        CRUD::setRoute($routePrefix . '/event');
        CRUD::setEntityNameStrings('event', 'events');
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

        CRUD::addColumn([
            'name' => 'applications_count',
            'label' => 'Applicants',
            'type' => 'number',
            'default' => 0,
            'wrapper' => [
                'class' => 'd-flex justify-content-end pe-5', // Align text to the right
            ],
        ]);

        CRUD::addColumn([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'custom_html',
            'value' => function ($entry) {
                $status = $entry->status;
                $colors = [
                    'draft' => 'secondary',   // Gray
                    'submitted' => 'info',    // Blue
                    // 'pending' => 'warning',   // Yellow
                    'approved' => 'success',  // Green
                    'rejected' => 'danger',   // Red
                ];
                $badgeColor = $colors[$status] ?? 'dark'; // Default to dark if status is unknown
        
                return '<span class="badge bg-'.$badgeColor.'">'.ucfirst($status).'</span>';
            },
            'escaped' => false, // Allows HTML rendering
        ]);
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
        ->type('date')
        ->label('Start Date');

        CRUD::field('end_date')
        ->type('date')
        ->label('End Date');

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
                'options' => \App\Models\Event::getStatuses(),
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

        if (backpack_user()->hasRole('Event Leader')) {
            $event = $this->crud->getCurrentEntry();
            if (!in_array($event->status, ['draft', 'rejected'])) {
                abort(403, 'You are not allowed to edit this event.');
            }
        }
    }
}
