<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use Prologue\Alerts\Facades\Alert;
use App\Http\Requests\SubmittedEventRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class EventApprovalCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SubmittedEventCrudController extends EventCrudController
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
        CRUD::setModel(Event::class);
        CRUD::setRoute(backpack_url('submitted-event'));
        CRUD::setEntityNameStrings('Submitted Event', 'Submitted Events');
        CRUD::setHeading('Submitted Events');

        // Only Office Admins can access this
        // if (!backpack_user()->can('approve events')) {
        //     abort(403);
        // }
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        parent::setupListOperation();
        
        CRUD::addClause('where', 'status', 'submitted');
        CRUD::removeButton('create');

        if (backpack_user()->can('approve events')) {
            CRUD::addButtonFromModelFunction('line', 'reject', 'rejectButton', 'end');
            CRUD::addButtonFromModelFunction('line', 'approve', 'approveButton', 'end');
        }

        if (!backpack_user()->hasRole('Superadmin')) {
            CRUD::removeButton('update');
            CRUD::removeButton('delete');
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
        CRUD::setValidation(SubmittedEventRequest::class);
        CRUD::setHeading($this->crud->getCurrentEntry()->title ?? 'Edit Submitted Event');
        // CRUD::setSubheading('Fill in event details & submit for approval');

        CRUD::field('title')
        ->type('text')
        ->label('Event Title');

        CRUD::field('description')
        ->type('textarea')
        ->label('Description');

        CRUD::field('location')
        ->type('text')
        ->label('Location');

        CRUD::field('start_date')
        ->label('Start Date')
        ->type('date')
        ->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('end_date')
        ->label('End Date')
        ->type('date')
        ->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('row_start')->type('custom_html')->value('<div class="row">');
        CRUD::field('max_participants')
        ->type('number')
        ->label('Maximum Participants');

        CRUD::field('cost')
        ->type('number')
        ->label('Cost')
        ->prefix('RM ');

        if (backpack_user() && backpack_user()->can('approve events')) {
            CRUD::addField([
                'name' => 'status',
                'label' => 'Status',
                'type' => 'select_from_array',
                'options' => Event::getStatuses(),
                'allows_null' => false, // Ensures a selection is made
                'default' => 'draft', // Default to Draft
            ]);
        }
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

    protected function setupShowOperation()
    {
        CRUD::setValidation(SubmittedEventRequest::class);
parent::setupShowOperation();
        CRUD::removeButton('update'); // Remove Edit button
        CRUD::removeButton('delete'); // Remove Delete button

        if (backpack_user()->can('approve events')) {
            CRUD::addButtonFromModelFunction('line', 'reject', 'rejectButtonShow', 'beginning');
            CRUD::addButtonFromModelFunction('line', 'approve', 'approveButtonShow', 'beginning');
        }
    }
}
