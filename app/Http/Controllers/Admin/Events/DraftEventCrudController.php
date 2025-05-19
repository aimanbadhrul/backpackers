<?php

namespace App\Http\Controllers\Admin\Events;

use App\Models\Event;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class EventSubmissionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DraftEventCrudController extends EventCrudController
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

        CRUD::addClause('whereIn', 'status', ['draft', 'rejected']);
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $user = backpack_user();
        parent::setupListOperation();
        
        CRUD::setEntityNameStrings('Event', 'Events');

        //Redundant
        // if (!$user->hasRole('Superadmin')) {
        //     CRUD::addClause('where', 'created_by', backpack_user()->id);
        // }

        CRUD::addButton('line', 'update', 'view', 'crud::buttons.update');
        CRUD::addButton('line', 'delete', 'view', 'crud::buttons.delete');

        CRUD::addButtonFromModelFunction('line', 'submit_for_approval', 'submitButton', 'end');
    }

    protected function setupCreateOperation()
    {
        parent::setupCreateOperation();
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        CRUD::setHeading('Edit Draft');
    }

    public function create()
    {
        return redirect(backpack_url('event/create'));
    }
}
