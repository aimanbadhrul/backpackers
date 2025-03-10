<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use App\Http\Requests\EventSubmissionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class EventSubmissionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EventSubmissionCrudController extends CrudController
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
        CRUD::setModel(Event::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/event-submission');
        CRUD::setEntityNameStrings('event submission', 'event submissions');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setEntityNameStrings('event', 'event');
        $this->crud->removeButton('create');

        if (backpack_user()->hasRole('Event Leader')) {
            CRUD::addClause('where', 'created_by', backpack_user()->id);
        }

        CRUD::addColumn(['name' => 'title', 'label' => 'Event Title']);
        CRUD::addColumn(['name' => 'createdBy.name', 'label' => 'Created By']);
        CRUD::addColumn(['name' => 'start_date', 'label' => 'Start Date']);
        CRUD::addColumn(['name' => 'end_date', 'label' => 'End Date']);
        
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
        CRUD::setValidation(EventSubmissionRequest::class);
        CRUD::setHeading('Submit Event');
        CRUD::setSubheading('Fill in event details & submit for approval');
        
        CRUD::field('title')
        ->type('text')
        ->label('Event Title')
        ->attributes(['required' => 'required']);

        CRUD::field('description')
        ->type('textarea')
        ->label('Description');

        CRUD::field('start_date')
        ->type('date')
        ->label('Start Date')
        ->attributes(['required' => 'required']);

        CRUD::field('end_date')
        ->type('date')
        ->label('End Date')
        ->attributes(['required' => 'required']);

        CRUD::field('max_participants')
        ->type('number')
        ->label('Maximum Participants')
        ->attributes(['min' => 1, 'required' => 'required']);

        CRUD::field('cost')
        ->type('number')
        ->label('Cost (RM)')
        ->prefix('RM ')
        ->attributes(['min' => 0, 'step' => '0.01']);

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
    }
}
