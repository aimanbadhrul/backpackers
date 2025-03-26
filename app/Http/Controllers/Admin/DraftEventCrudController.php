<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class EventSubmissionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DraftEventCrudController extends CrudController
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
        CRUD::setRoute(backpack_url('draft-event'));
        CRUD::setEntityNameStrings('Draft Event', 'Draft Events');
        CRUD::setHeading('Drafts');
        CRUD::setSubheading('Manage your event drafts');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setEntityNameStrings('Event', 'Events');
        // $this->crud->removeButton('create');
        // CRUD::setCreateButtonRoute('admin.event.create');

        if (backpack_user()->hasRole('Event Leader')) {
            CRUD::addClause('where', 'created_by', backpack_user()->id);
        }

        CRUD::addClause('whereIn', 'status', ['draft', 'rejected']);

        CRUD::addColumn(['name' => 'title', 'label' => 'Event Title']);
        CRUD::addColumn(['name' => 'createdBy.name', 'label' => 'Created By']);
        CRUD::addColumn(['name' => 'start_date', 'label' => 'Start Date']);
        CRUD::addColumn(['name' => 'end_date', 'label' => 'End Date']);
        
        CRUD::addColumn([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'custom_html',
            'value' => fn($entry) => $entry->getStatusBadge(),
            'escaped' => false, // Allows HTML rendering
        ]);

        CRUD::addButtonFromModelFunction('line', 'submit_for_approval', 'submitButton', 'end');
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {

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

    public function create()
{
    return redirect(backpack_url('event/create'));
}
}
