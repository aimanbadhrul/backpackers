<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Event;
use App\Models\Application;
use Prologue\Alerts\Facades\Alert;
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
        CRUD::setEntityNameStrings('Application', 'Applications');
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

        if ($user->hasRole('Event Leader')) {
                $eventIds = Event::where('created_by', $user->id)->pluck('id');
                $this->crud->addClause('whereIn', 'event_id', $eventIds);
        }
        

        // CRUD::setFromDb(); // set columns from db columns.
        CRUD::addColumn([
            'label' => "Event",
            'type' => "select",
            'name' => 'event_id',
            'entity' => 'event',
            'attribute' => 'title',
            'model' => Event::class,
        ]);
        CRUD::addColumn([
            'label' => "Applicant Name",
            'type' => "select",
            'name' => 'user_id', // The foreign key column in applications table
            'entity' => 'user', // The relationship method in Application model
            'attribute' => 'name', // The column to display in the table
            'model' => User::class,
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
        CRUD::column('approval_date')->label('Approved At');
        CRUD::column('payment_status')->label('Payment');
        
        if ($user->hasRole('Event Leader') || $user->hasRole('Superadmin')) {
        CRUD::addButtonFromModelFunction('line', 'reject', 'rejectButton', 'end');
        CRUD::addButtonFromModelFunction('line', 'approve', 'approveButton', 'end');
        }
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
            'model'     => Event::class,
            'tab' => 'Details'
        ]);
    
        CRUD::addField([
            'label' => "Applicant",
            'type'  => 'select',
            'name'  => 'user_id',
            'entity' => 'user',
            'attribute' => 'name',
            'model' => User::class,
            'tab' => 'Details'
        ]);

        CRUD::addField([
            'name' => 'full_name',
            'label' => 'Full Name',
            'type' => 'text',
            'tab' => 'Details'
        ]);
    
        CRUD::addField([
            'name' => 'email',
            'label' => 'Email',
            'type' => 'email',
            'tab' => 'Details'
        ]);

        CRUD::addField([
            'name' => 'phone',
            'label' => 'Phone',
            'type' => 'text',
            'tab' => 'Details'
        ]);
    
        CRUD::addField([
            'name' => 'emergency_contact_name',
            'label' => 'Emergency Contact Name',
            'type' => 'text',
            'tab' => 'Details'
        ]);
    
        CRUD::addField([
            'name' => 'emergency_contact_phone',
            'label' => 'Emergency Contact Phone',
            'type' => 'text',
            'tab' => 'Details'
        ]);

        CRUD::addField([
            'name' => 'special_requests',
            'label' => 'Special Requests',
            'type' => 'textarea',
            'tab' => 'Details'
        ]);

        CRUD::addField([
            'name'  => 'notes',
            'label' => 'Notes',
            'type'  => 'textarea',
            'tab' => 'Details'
        ]);

        CRUD::addField([
            'name' => 'empty_tab_placeholder',
            'type' => 'custom_html',
            'value' => '',
            'tab' => 'Payment Details',
        ]);

        CRUD::addField([
            'name'    => 'status',
            'label'   => 'Status',
            'type'    => 'select_from_array',
            'options' => Application::getStatusOptions(), // Fetch dynamically
            'tab'     => 'Status'
        ]);
    
        CRUD::addField([
            'name' => 'rejection_reason',
            'label' => 'Rejection Reason',
            'type' => 'textarea',
            'hint' => 'Required if rejected.',
            'tab' => 'Status'
        ]);
    
        CRUD::addField([
            'name' => 'payment_status',
            'label' => 'Payment Status',
            'type' => 'select_from_array',
            'options' => ['pending' => 'Pending', 'paid' => 'Paid', 'waived' => 'Waived'],
            'allows_null' => false,
            'default' => 'pending',
            'tab' => 'Status'
        ]);

        CRUD::addField([
            'name' => 'group',
            'label' => 'Group',
            'type' => 'text',
            'tab' => 'Status'
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

    public function approve($id)
    {
        $application = Application::findOrFail($id);
        $event = $application->event;

        $approvedCount = Application::where('event_id', $event->id)
        ->where('status', 'approved')
        ->count();

        if ($approvedCount >= $event->max_participants) {
            Alert::error("Maximum participant limit ({$event->max_participants}) exceeded!")->flash();
            return redirect()->back();
        }

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

    public function confirm(Application $application)
{
    if ($application->status !== 'approved') {
        Alert::error('Only approved applications can be confirmed!')->flash();
        return redirect()->back();
    }

    if ($application->payment_status === 'pending') {
        Alert::error('Payment must be completed before confirming!')->flash();
        return redirect()->back();
    }

    $application->update(['status' => 'confirmed']);

    Alert::success('Application successfully confirmed!')->flash();
    return redirect()->back();
}
    
    protected function setupShowOperation()
    {
        CRUD::column('event_id')->label('Event');
        CRUD::column('user_id')->label('User');
        CRUD::column('full_name')->label('Full Name');
        CRUD::column('email')->label('Email');
        CRUD::column('phone')->label('Phone');
        CRUD::column('emergency_contact_name')->label('Emergency Contact Name');
        CRUD::column('emergency_contact_phone')->label('Emergency Contact Phone');
        CRUD::column('submission_date')->label('Submission Date');
        CRUD::column('approval_date')->label('Approval Date');
        CRUD::column('status')->label('Status');
        CRUD::column('rejection_reason')->label('Rejection Reason');
        CRUD::column('payment_status')->label('Payment Status');
        CRUD::column('group')->label('Group');
        CRUD::column('special_requests')->label('Special Requests');
        CRUD::column('notes')->label('Notes');
    }


}
