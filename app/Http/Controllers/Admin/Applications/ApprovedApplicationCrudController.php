<?php

namespace App\Http\Controllers\Admin\Applications;

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
        CRUD::setModel(Application::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/approved-application');
        CRUD::setEntityNameStrings('approved application', 'approved applications');

        CRUD::addClause('where', 'status', 'approved');
    }

    protected function setupListOperation()
    {
        $user = backpack_user();
        CRUD::removeButton('create');
        parent::setupListOperation();

        CRUD::addColumn([
            'name' => 'payment_status',
            'label' => 'Payment Status',
            'type' => 'text'
        ]);

        if ($user->can('approve applications')){
        CRUD::addButton('line', 'update', 'view', 'crud::buttons.update');

        CRUD::addButtonFromModelFunction('line', 'confirm', 'getConfirmButtonHtml', 'end');
        }

        if($user->hasRole('Superadmin')){
            CRUD::addButton('line', 'delete', 'view', 'crud::buttons.delete');
        }
    }
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ApprovedApplicationRequest::class);
        CRUD::setFromDb(); // set fields from db columns.
    }
    
    protected function setupUpdateOperation()
    {
        parent::setupCreateOperation();
    }

    protected function setupShowOperation()
    {
        parent::setupShowOperation();
    }
}
