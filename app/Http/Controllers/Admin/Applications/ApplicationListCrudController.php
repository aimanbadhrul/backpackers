<?php

namespace App\Http\Controllers\Admin\Applications;

use App\Models\User;
use App\Models\Event;
use App\Models\Application;
use App\Http\Requests\ApplicationListRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ApplicationListCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ApplicationListCrudController extends ApplicationCrudController
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
        CRUD::setRoute(config('backpack.base.route_prefix') . '/application-list');
        CRUD::setEntityNameStrings('Application', 'Applications');

        $user = backpack_user();

        CRUD::addClause('where', 'user_id', $user->id);        
    }

    public function create()
    {
        return redirect(backpack_url('application/create'));
    }

    // public function show($id)
    // {
    //     return redirect(backpack_url("application/{$id}/show"));
    // }
    protected function setupListOperation()
    {
        $user = backpack_user();
        CRUD::removeButton('update');
        CRUD::removeButton('delete');

        CRUD::addColumn([
            'label' => "Event",
            'type' => "select",
            'name' => 'event_id',
            'entity' => 'event',
            'attribute' => 'title',
            'model' => Event::class,
        ]);

        CRUD::addColumn([
            'label' => "User",
            'type' => "select",
            'name' => 'user_id',
            'entity' => 'user',
            'attribute' => 'name',
            'model' => User::class,
        ]);    
        
        CRUD::addColumn([
            'name' => 'full_name',
            'label' => 'Applicant Name',
            'type' => 'text'
        ]);

        CRUD::addColumn([
            'name' => 'created_at',
            'label' => 'Submitted At',
            'type' => 'datetime'
        ]);

        CRUD::addColumn([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'select_from_array',
            'options' => Application::getStatusOptions(),
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    $colors = [
                        'pending'  => 'badge bg-warning',  // Yellow
                        'approved' => 'badge bg-success',  // Green
                        'confirmed' => 'badge bg-primary',  // Blue
                        'rejected' => 'badge bg-danger',   // Red
                        'completed' => 'badge bg-dark',  // Dark
                    ];
                    return $colors[$entry->status] ?? 'badge bg-secondary';
                },
            ],
        ]);

        CRUD::addColumn([
            'name' => 'approval_date',
            'label' => 'Approved At',
            'type' => 'datetime'
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

    }
    protected function setupUpdateOperation()
    {
        parent::setupUpdateOperation();
    }

    protected function setupShowOperation()
    {
        parent::setupShowOperation();
        CRUD::addButton('line', 'update', 'view', 'crud::buttons.update');
        CRUD::addButton('line', 'delete', 'view', 'crud::buttons.delete');
    }
}
