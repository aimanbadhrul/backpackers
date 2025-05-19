<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use App\Http\Requests\PermissionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PermissionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PermissionCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    protected $model = Permission::class;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        if (!backpack_user() || !backpack_user()->can('manage permissions')) {
            // Deny access to all CRUD operations
            CRUD::denyAccess(['list', 'create', 'update', 'delete', 'show']);
            return;
        }

        CRUD::setModel(Permission::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/permission');
        CRUD::setEntityNameStrings('permission', 'permissions');
    }

    protected function setupListOperation()
    {
        CRUD::setFromDb(); // set columns from db columns.

        CRUD::addColumn([
            'name' => 'roles_count',
            'label' => 'Roles Assigned',
            'type' => 'model_function',
            'function_name' => 'getRolesCount',
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
        CRUD::setValidation(PermissionRequest::class);
        // CRUD::setFromDb(); // set fields from db columns.
        CRUD::addField([
            'name'  => 'name',
            'label' => 'Permission Name',
            'type'  => 'text',
        ]);

        CRUD::addField([
            'name' => 'guard_name',
            'label' => 'Guard Name',
            'type' => 'text',
            'attributes' => [
                'readonly' => 'readonly'],
            'default' => 'backpack',
        ]);

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
}
