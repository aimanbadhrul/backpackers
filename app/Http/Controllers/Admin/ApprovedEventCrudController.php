<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EventRequest;
use App\Http\Requests\ApprovedEventRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ApprovedEventCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ApprovedEventCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Event::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/approved-event');
        CRUD::setEntityNameStrings('approved event', 'approved events');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->removeButton('create');

        if (backpack_user()->hasRole('Event Leader')) {
            CRUD::addClause('where', 'created_by', backpack_user()->id);
        }

        // Only show approved events
        CRUD::addClause('where', 'status', 'approved');

        CRUD::addColumn(['name' => 'title', 'label' => 'Event Title']);
        CRUD::addColumn(['name' => 'createdBy.name', 'label' => 'Created By']);
        CRUD::addColumn(['name' => 'start_date', 'label' => 'Start Date']);
        CRUD::addColumn(['name' => 'end_date', 'label' => 'End Date']);

        CRUD::addColumn([
            'name' => 'dashboard_link',
            'label' => 'Event Dashboard',
            'type' => 'custom_html',
            'value' => function ($entry) {
                return '<a href="' . route('admin.event-dashboard', $entry->id) . '" class="btn btn-sm btn-primary">View Dashboard</a>';
            },
            'escaped' => false,
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
        CRUD::setValidation(ApprovedEventRequest::class);
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
        CRUD::setValidation(EventRequest::class);
        CRUD::setHeading($this->crud->getCurrentEntry()->title ?? 'Edit Approved Event');
        CRUD::setSubheading('Details added here will be displayed on the Event Dashboard');

        CRUD::field('title')->attributes(['disabled' => 'disabled'])->tab('Details');
        CRUD::field('description')->attributes(['disabled' => 'disabled'])->tab('Details');
        CRUD::field('location')->attributes(['disabled' => 'disabled'])->tab('Details');
        CRUD::field('start_date')->wrapper(['class' => 'form-group col-md-3'])->attributes(['disabled' => 'disabled'])->tab('Details');
        CRUD::field('end_date')->wrapper(['class' => 'form-group col-md-3'])->attributes(['disabled' => 'disabled'])->tab('Details');
        CRUD::field('max_participants')->attributes(['disabled' => 'disabled'])->tab('Details');
        CRUD::field('cost')->label('Cost (RM)')->attributes(['disabled' => 'disabled'])->tab('Details');
        CRUD::addField([
            'name' => 'empty_tab_placeholder',
            'type' => 'custom_html',
            'value' => '',
            'tab' => 'Places of Interest',
        ]);
        CRUD::addField([
            'name' => 'empty_tab_placeholder_2',
            'type' => 'custom_html',
            'value' => '',
            'tab' => 'Item Checklist',
        ]);
        CRUD::addField([
            'name' => 'empty_tab_placeholder_3',
            'type' => 'custom_html',
            'value' => '',
            'tab' => 'Participants List',
        ]);
        CRUD::addField([
            'name' => 'empty_tab_placeholder_4',
            'type' => 'custom_html',
            'value' => '',
            'tab' => 'Additional Information',
        ]);
    }
}
