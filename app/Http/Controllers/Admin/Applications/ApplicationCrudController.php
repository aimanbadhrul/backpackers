<?php

namespace App\Http\Controllers\Admin\Applications;

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

        CRUD::addClause('where', 'status', '!=', 'completed');
        
        $user = backpack_user();
        if (!$user->hasRole('Superadmin')) {
            CRUD::denyAccess('delete');
        }
    }
    protected function setupListOperation()
    {
        $user = backpack_user();

        if (!$user->can('view all applications')) {
            $userId = $user->id;
        
            CRUD::addClause('where', function ($query) use ($userId) {
                $query->whereIn('event_id', Event::where('created_by', $userId)->pluck('id'))->orWhere('user_id', $userId);;
            });
        }        

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
            'name' => 'user_id', // The foreign key column in applications table
            'entity' => 'user', // The relationship method in Application model
            'attribute' => 'name', // The column to display in the table
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

        // if (!backpack_user()->hasRole('Superadmin')) {
            // Remove all default action buttons
            CRUD::removeButton('update');
            CRUD::removeButton('delete');
            // CRUD::removeButton('show');
        // }
    }

    protected function setupCreateOperation()
    {
        $user = backpack_user();
        CRUD::setValidation(ApplicationRequest::class);
        
        CRUD::addField([
            'label'     => "Event",
            'type'      => 'select',
            'name'      => 'event_id', // Foreign key in applications table
            'entity'    => 'event',
            'attribute' => 'display_title',
            'model'     => Event::class,
            'tab'       => 'Details',
            'options'   => fn($query) => $query->where('status', 'approved')->get(),
        ]);
        
        CRUD::addField([
            'label' => "User",
            'type' => $user->hasRole('Superadmin') ? 'select' : 'hidden',
            'name'  => 'user_id',
            'entity' => 'user',
            'attribute' => 'name',
            'model' => User::class,
            'default' => $user->id,
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

        if (
            $user->hasRole('Superadmin') ||
            (isset($this->crud->getCurrentEntry()->event) && $this->crud->getCurrentEntry()->event->created_by == $user->id)
        ) {
            CRUD::addField([
            'name' => 'payment_receipt',
            'label' => 'Payment Receipt',
            'type' => 'upload',
            'upload' => true,
            'disk' => 'public',
            'tab' => 'Approval'
            ]);
    
            CRUD::addField([
            'name' => 'payment_status',
            'label' => 'Payment Status',
            'type' => 'select_from_array',
            'options' => ['pending' => 'Pending', 'paid' => 'Paid', 'waived' => 'Waived'],
            'allows_null' => false,
            'default' => 'pending',
            'tab' => 'Approval'
            ]);

            CRUD::addField([
            'name'    => 'status',
            'label'   => 'Status',
            'type'    => 'select_from_array',
            'options' => Application::getStatusOptions(), // Fetch dynamically
            'tab'     => 'Approval',
            'default' => 'pending'
            ]);
        }
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function setupShowOperation()
    {
        $entry = CRUD::getCurrentEntry();
        $event = $entry->event;
        $user = backpack_user();

        CRUD::removeButton('update');
        CRUD::removeButton('delete');

        // Tab: Application Details
        CRUD::addColumn([
            'name' => 'event_title',
            'label' => 'Event',
            'type' => 'custom_html',
            'tab' => 'Details',
            'value' => e($event->title)
        ]);

        CRUD::addColumn([
            'name'  => 'user_id',
            'label' => "User",
            'type'  => 'custom_html',
            'tab' => 'Details',
            'value' => e($user->name),
        ]);

        CRUD::addColumn([
            'name' => 'full_name',
            'label' => 'Full Name',
            'type' => 'custom_html',
            'tab' => 'Details',
            'value' => e($entry->full_name)
        ]);

        CRUD::addColumn([
            'name' => 'email',
            'label' => 'Email',
            'type' => 'custom_html',
            'tab' => 'Details',
            'value' => e($entry->email)
        ]);

        CRUD::addColumn([
            'name' => 'phone',
            'label' => 'Phone No',
            'type' => 'custom_html',
            'tab' => 'Details',
            'value' => e($entry->phone)
        ]);

        CRUD::addColumn([
            'name' => 'special_requests',
            'label' => 'Special Requests',
            'type' => 'custom_html',
            'tab' => 'Details',
            'value' => e($entry->special_requests)
        ]);

        CRUD::addColumn([
            'name' => 'submission_date',
            'label' => 'Submission Date',
            'type' => 'custom_html',
            'tab' => 'Details',
            'value' => e($entry->created_at->format('d M Y'))
        ]);

        CRUD::addColumn([
            'name' => 'approval_date',
            'label' => 'Approval Date',
            'type' => 'custom_html',
            'tab' => 'Details',
            'value' => e($entry->approval_date)
        ]);

        CRUD::addColumn([
            'name' => 'payment_status',
            'label' => 'Payment Status',
            'type' => 'custom_html',
            'tab' => 'Details',
            'value' => e($entry->payment_status)
        ]);

        // Tab: Emergency Contact
        CRUD::addColumn([
            'name' => 'emergency_name',
            'label' => 'Contact Name',
            'type' => 'custom_html',
            'tab' => 'Emergency Contact',
            'value' => e($entry->emergency_contact_name)
        ]);

        CRUD::addColumn([
            'name' => 'emergency_no',
            'label' => 'Contact No',
            'type' => 'custom_html',
            'tab' => 'Emergency Contact',
            'value' => e($entry->emergency_contact_phone)
        ]);

        // Tab: Event Details
        CRUD::addColumn([
            'name' => 'event_title_2',
            'label' => 'Event Title',
            'type' => 'custom_html',
            'tab' => 'Event Info',
            'value' => e($event->title)
        ]);

        CRUD::addColumn([
            'name' => 'event_location',
            'label' => 'Location',
            'type' => 'custom_html',
            'tab' => 'Event Info',
            'value' => e($event->location)
        ]);

        CRUD::addColumn([
            'name' => 'event_start_date',
            'label' => 'Start Date',
            'type' => 'custom_html',
            'tab' => 'Event Info',
            'value' => e($event->start_date->format('d M Y'))
        ]);

        CRUD::addColumn([
            'name' => 'event_end_date',
            'label' => 'End Date',
            'type' => 'custom_html',
            'tab' => 'Event Info',
            'value' => e($event->end_date->format('d M Y'))
        ]);

        CRUD::addColumn([
            'name' => 'event_status',
            'label' => 'Status',
            'type' => 'custom_html',
            'tab' => 'Event Info',
            'value' => e($event->status)
        ]);
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
        $application->approval_date = now();
        $application->save();

        Alert::success('Application approved!')->flash();
        return redirect()->route('pending-application.index');
    }
    
    public function reject($id)
    {
        $application = Application::findOrFail($id);
        $application->update(['status' => 'rejected']);
    
        Alert::error('Application rejected!')->flash();
        return redirect()->route('pending-application.index');
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

}
