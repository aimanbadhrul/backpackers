<?php

namespace App\Http\Controllers\Admin\Events;

use App\Models\User;
use App\Models\Event;
use App\Http\Requests\EventListRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class EventListCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EventListCrudController extends CrudController
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
        CRUD::setRoute(config('backpack.base.route_prefix') . '/event-list');
        CRUD::setEntityNameStrings('event list', 'event lists');

        CRUD::addClause('where', 'status', 'approved');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::removeButton('create');
        CRUD::addColumn([
            'name'  => 'title',  // Relationship method
            'label' => 'Event Title',
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
            'name' => 'max_participants',
            'label' => 'Max Participants',
            'type' => 'number',
            'default' => 0,
            // 'wrapper' => [
            //     'class' => 'd-flex justify-content-end pe-5',
            //     ],
        ]);

        CRUD::addColumn([
            'name' => 'applications_count',
            'label' => 'Applicants',
            'type' => 'number',
            'default' => 0,
            // 'wrapper' => [
            // 'class' => 'd-flex justify-content-end pe-5',
            // ],
        ]);

        CRUD::removeButton('update');
        CRUD::removeButton('delete');
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(EventListRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
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
    }
}
