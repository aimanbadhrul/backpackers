<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Permission;
use App\Http\Requests\RoleRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class RoleCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RoleCrudController extends CrudController
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
    protected $model = Role::class;
    public function setup()
    {
        CRUD::setModel(Role::class);
        CRUD::setRoute(backpack_url('role'));
        CRUD::setEntityNameStrings('role', 'roles');
        CRUD::setColumns(['name']);
        CRUD::addField(['name' => 'name', 'type' => 'text', 'label' => 'Role Name']);
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name')->label('Role Name');
        CRUD::column('guard_name')->label('Guard');

        CRUD::addColumn([
            'name' => 'permissions',
            'label' => 'Permissions',
            'type' => 'text',
            'value' => function ($entry) {
                return $entry->permissions->pluck('name')->join(', ');
            },
        ]);

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(RoleRequest::class);
        
        CRUD::addField([
            'name'  => 'name',
            'label' => 'Role Name',
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
    
        CRUD::addField([
            'label' => "Permissions",
            'type' => 'checklist',
            'name' => 'permissions', // Spatie relationship
            'entity' => 'permissions', // The method in Role model
            'attribute' => 'name', // Show permission names
            'model' => Permission::class, 
            'pivot' => true, // Many-to-many relationship
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

    protected function setupShowOperation()
    {
        CRUD::addColumn([
            'name' => 'name',
            'label' => 'Role Name',
        ]);
        CRUD::column('guard_name')->label('Guard');
        CRUD::addColumn([
            'name' => 'permissions',
            'label' => 'Permissions',
            'type' => 'custom_html',
            'value' => function ($entry) {
                return '<ul>' . collect($entry->permissions)->map(fn($perm) => "<li>{$perm->name}</li>")->join('') . '</ul>';
            },
        ]);
    }

}
