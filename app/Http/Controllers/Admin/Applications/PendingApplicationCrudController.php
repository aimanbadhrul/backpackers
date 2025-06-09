<?php

namespace App\Http\Controllers\Admin\Applications;

use App\Models\Event;
use App\Models\Application;
use App\Http\Requests\PendingApplicationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PendingApplicationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PendingApplicationCrudController extends ApplicationCrudController
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
        CRUD::setModel(\App\Models\PendingApplication::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/pending-application');
        CRUD::setEntityNameStrings('pending application', 'pending applications');
        CRUD::addClause('where', 'status', 'pending');
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
        CRUD::removeButton('create');

        if ($user->hasRole('Event Leader')) {
                $eventIds = Event::where('created_by', $user->id)->pluck('id');
                $this->crud->addClause('whereIn', 'event_id', $eventIds);
        }
        
        parent::setupListOperation();

        if ($user->hasRole('Superadmin')) {
        CRUD::addButton('line', 'update', 'view', 'crud::buttons.update');
        CRUD::addButton('line', 'delete', 'view', 'crud::buttons.delete');
        }

        CRUD::addButtonFromModelFunction('line', 'reject', 'rejectButton', 'end');
        CRUD::addButtonFromModelFunction('line', 'approve', 'approveButton', 'end');

    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(PendingApplicationRequest::class);
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
        parent::setupCreateOperation();
    }

    protected function setupShowOperation()
    {
        parent::setupShowOperation();
        CRUD::removeButton('update'); // Remove Edit button
        CRUD::removeButton('delete'); // Remove Delete button

        
            CRUD::addButtonFromModelFunction('line', 'reject', 'rejectButtonShow', 'end');
            CRUD::addButtonFromModelFunction('line', 'approve', 'approveButtonShow', 'beginning');
        
    }
}
