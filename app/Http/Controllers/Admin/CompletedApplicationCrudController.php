<?php

namespace App\Http\Controllers\Admin;

use App\Models\Application;
use App\Http\Requests\CompletedApplicationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CompletedApplicationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CompletedApplicationCrudController extends ApplicationCrudController
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
        CRUD::setModel(Application::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/completed-application');
        CRUD::setEntityNameStrings('Completed Application', 'Completed Applications');


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
        CRUD::addClause('where', 'status', 'completed');

        parent::setupListOperation();

        CRUD::removeButton('create');
        if ($user->hasRole('Superadmin')) {
            CRUD::addButton('line', 'update', 'view', 'crud::buttons.update');
            CRUD::addButton('line', 'delete', 'view', 'crud::buttons.delete');
        }
    }
    protected function setupCreateOperation()
    {
        abort(403);
    }
    protected function setupUpdateOperation()
    {
        abort(403);
    }
    protected function setupShowOperation()
    {
        parent::setupShowOperation();
    }
}
