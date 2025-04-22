<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ApprovedApplicationRequest;
use App\Models\Application;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ApprovedApplicationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ApprovedApplicationCrudController extends ApplicationCrudController
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
        CRUD::setModel(\App\Models\ApprovedApplication::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/approved-application');
        CRUD::setEntityNameStrings('approved application', 'approved applications');
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
        parent::setupListOperation();
        CRUD::addClause('where', 'status', 'approved');
        CRUD::addButton('line', 'update', 'view', 'crud::buttons.update');
        CRUD::addButton('line', 'delete', 'view', 'crud::buttons.delete');

        CRUD::addButtonFromModelFunction('line', 'confirm', 'confirmButton', 'end');
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ApprovedApplicationRequest::class);
        CRUD::setFromDb(); // set fields from db columns.
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
    }
}
