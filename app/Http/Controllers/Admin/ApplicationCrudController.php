<?php

namespace App\Http\Controllers\Admin;

use Prologue\Alerts\Facades\Alert;
use App\Models\Application;
use App\Http\Requests\ApplicationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;


/**
 * Class ApplicationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ApplicationCrudController extends CrudController
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
        CRUD::setRoute(config('backpack.base.route_prefix') . '/application');
        CRUD::setEntityNameStrings('application', 'applications');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // CRUD::setFromDb(); // set columns from db columns.
        CRUD::addColumn([
            'label' => "Applicant Name",
            'type' => "select",
            'name' => 'user_id', // The foreign key column in applications table
            'entity' => 'user', // The relationship method in Application model
            'attribute' => 'name', // The column to display in the table
            'model' => \App\Models\User::class,
        ]);
        CRUD::addColumn([
            'label' => "Event",
            'type' => "select",
            'name' => 'event_id',
            'entity' => 'event',
            'attribute' => 'title',
            'model' => \App\Models\Event::class,
        ]);
                
        CRUD::addColumn([
            'name' => 'created_at',
            'label' => 'Submitted At',
            'type' => 'datetime', // Formats automatically
            'format' => 'MMM DD, YYYY HH:mm A', // Example: Feb 26, 2025 10:30 AM
        ]);
        
        CRUD::addColumn([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'select_from_array',
            'options' => [
                'pending'  => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
            ],
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    $colors = [
                        'pending'  => 'badge bg-warning',  // Yellow
                        'approved' => 'badge bg-success',  // Green
                        'rejected' => 'badge bg-danger',   // Red
                    ];
                    return $colors[$entry->status] ?? 'badge bg-secondary';
                },
            ],
        ]);

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
        CRUD::setValidation(ApplicationRequest::class);
        
        CRUD::addField([
            'label'     => "Event",
            'type'      => 'select',
            'name'      => 'event_id', // The foreign key column in the applications table
            'entity'    => 'event',
            'attribute' => 'title', // The column to display in the dropdown
            'model'     => \App\Models\Event::class,
        ]);
    
        CRUD::addField([
            'label' => "Applicant",
            'type'  => 'select',
            'name'  => 'user_id',
            'entity' => 'user',
            'attribute' => 'name',
            'model' => \App\Models\User::class,
        ]);
    
        CRUD::addField([
            'name'  => 'status',
            'label' => 'Status',
            'type'  => 'select_from_array',
            'options' => [
                'pending'  => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
            ],
            'sortable'
        ]);
    
        CRUD::addField([
            'name'  => 'notes',
            'label' => 'Notes',
            'type'  => 'textarea',
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

        CRUD::field('status')->type('select_from_array')
        ->options([
            'pending'  => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ])
        ->label('Status');
    }

    public function approve($id)
    {
        $application = Application::findOrFail($id);
        $application->update(['status' => 'approved']);
    
        Alert::success('Application approved!')->flash();
        return redirect()->back();
    }
    
    public function reject($id)
    {
        $application = Application::findOrFail($id);
        $application->update(['status' => 'rejected']);
    
        Alert::error('Application rejected!')->flash();
        return redirect()->back();
    }
    

}
