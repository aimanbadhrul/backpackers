<?php

namespace App\Http\Controllers\Admin;
use App\Models\User;
use App\Http\Requests\UserRequest;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Backpack\CRUD\app\Library\Widget;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserCrudController extends CrudController
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
        $routePrefix = config('backpack.base.route_prefix');
        CRUD::setModel(User::class);
        CRUD::setRoute($routePrefix . '/user');
        CRUD::setEntityNameStrings('user', 'users');

        parent::setup();

        // Get dynamic data
        $userCount = User::count();

        // Add a row div for better layout
        Widget::add()->to('before_content')->type('div')->class('row mb-3')->content([

        // Progress widget
        Widget::make()
            ->type('progress')
            ->class('card border-0 text-white bg-primary')
            ->progressClass('progress-bar')
            ->value($userCount)
            ->description('Registered users.')
            ->progress(100 * (int)$userCount / 1000)
            ->hint(1000 - $userCount . ' more until next milestone.'),
        ]);

    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {        
    CRUD::addColumn([
        'name' => 'name',
        'label' => 'Name',
        'type' => 'text',
    ]);

    CRUD::addColumn([
        'name' => 'email',
        'label' => 'Email',
        'type' => 'email',
    ]);

    CRUD::addColumn([
        'name' => 'roles',
        'label' => 'Role',
        'type' => 'select_multiple',
        'entity' => 'roles',
        'attribute' => 'name',
        'model' => "Spatie\Permission\Models\Role"
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
        CRUD::setValidation(UserRequest::class);
        // CRUD::setFromDb(); // set fields from db columns.

        CRUD::addField([
            'name' => 'name',
            'label' => 'Name',
            'type' => 'text'
        ]);

        CRUD::addField([
            'name' => 'email',
            'label' => 'Email',
            'type' => 'email'
        ]);

        CRUD::addField([
            'name' => 'password',
            'label' => 'Password',
            'type' => 'password'
        ]);

        CRUD::addField([
            'name' => 'password_confirmation',
            'label' => 'Confirm Password',
            'type' => 'password',
        ]);

        CRUD::addField([
            'name' => 'roles',
            'label' => 'Roles',
            'type' => 'checklist',
            'entity' => 'roles',
            'attribute' => 'name',
            'model' => "Spatie\Permission\Models\Role",
            'pivot' => true
        ]);
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

//     public function store()
//     {
//         $request = $this->crud->getRequest();

//         $validated = $request->validate([
//             'name' => 'required|string|max:255',
//             'email' => 'required|email|unique:users',
//             'password' => 'required|min:6|confirmed',
//             'role' => 'required|exists:roles,name',
//         ]);

//         $user = User::create([
//             'name' => $validated['name'],
//             'email' => $validated['email'],
//             'password' => Hash::make($validated['password']),
//         ]);

//         $user->syncRoles($validated['roles'] ?? []);

//         return redirect()->back()->with('success', 'User registered successfully.');
//     }

//     public function update()
// {
//     $request = $this->crud->getRequest();
//     $user = User::findOrFail($request->id);

//     $validated = $request->validate([
//         'name' => 'required|string|max:255',
//         'email' => 'required|email|unique:users,email,' . $user->id,
//         'password' => 'nullable|min:6|confirmed',
//         'roles' => 'required|exists:roles,name',
//     ]);

//     $user->update([
//         'name' => $validated['name'],
//         'email' => $validated['email'],
//         'password' => $validated['password'] ? Hash::make($validated['password']) : $user->password,
//     ]);

//     $user->syncRoles($validated['roles'] ?? []);

//     return redirect()->back()->with('success', 'User updated successfully.');
// }
}
