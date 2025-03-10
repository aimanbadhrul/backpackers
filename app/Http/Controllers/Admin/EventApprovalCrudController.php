<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use Prologue\Alerts\Facades\Alert;
use App\Http\Requests\EventApprovalRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class EventApprovalCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EventApprovalCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Event::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/event-approval');
        CRUD::setEntityNameStrings('event approval', 'event approvals');

        // Only Office Admins can access this
        if (!backpack_user()->can('approve events')) {
            abort(403);
        }
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->removeButton('create');
        CRUD::addColumn(['name' => 'title', 'label' => 'Event Title']);
        CRUD::addColumn(['name' => 'createdBy.name', 'label' => 'Created By']);
        CRUD::addColumn(['name' => 'location', 'label' => 'Location']);
        CRUD::addColumn(['name' => 'start_date', 'label' => 'Start Date']);
        CRUD::addColumn(['name' => 'end_date', 'label' => 'End Date']);
        
        CRUD::addColumn([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'custom_html',
            'value' => function ($entry) {
                $status = $entry->status;
                $colors = [
                    'draft' => 'secondary',   // Gray
                    'submitted' => 'info',    // Blue
                    // 'pending' => 'warning',   // Yellow
                    'approved' => 'success',  // Green
                    'rejected' => 'danger',   // Red
                ];
                $badgeColor = $colors[$status] ?? 'dark'; // Default to dark if status is unknown
        
                return '<span class="badge bg-'.$badgeColor.'">'.ucfirst($status).'</span>';
            },
            'escaped' => false, // Allows HTML rendering
        ]);

        // Show only events that are "submitted"
        $this->crud->addClause('where', 'status', 'submitted');

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
        CRUD::setValidation(EventApprovalRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

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
        $event = Event::findOrFail($id);
        $event->status = 'approved';
        $event->save();

        Alert::success('Event Approved!')->flash();
        return redirect()->back();
    }

    public function reject($id)
    {
        $event = Event::findOrFail($id);
        $event->status = 'rejected';
        $event->save();

        Alert::error('Event Rejected!')->flash();
        return redirect()->back();
    }
}
