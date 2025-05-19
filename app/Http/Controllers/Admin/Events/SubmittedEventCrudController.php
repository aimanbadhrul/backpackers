<?php

namespace App\Http\Controllers\Admin\Events;

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

        CRUD::addClause('where', 'status', 'submitted');
    }
    protected function setupListOperation()
    {
        $user = backpack_user();
        parent::setupListOperation();
        
        CRUD::removeButton('create');
        if ($user->hasRole('Superadmin')) {
            CRUD::addButton('line', 'update', 'view', 'crud::buttons.update');
            CRUD::addButton('line', 'delete', 'view', 'crud::buttons.delete');
            }

        if (backpack_user()->can('approve events')) {
            CRUD::addButtonFromModelFunction('line', 'reject', 'rejectButton', 'end');
            CRUD::addButtonFromModelFunction('line', 'approve', 'approveButton', 'end');
        }
    }
    protected function setupCreateOperation()
    {
        parent::setupCreateOperation();
    }
    protected function setupUpdateOperation()
    {
        parent::setupUpdateOperation();
        CRUD::setHeading($this->crud->getCurrentEntry()->title ?? 'Edit Submitted Event');
        CRUD::setSubheading('Event already submitted');
    }
    protected function setupShowOperation()
    {
        parent::setupShowOperation();
    }

    public function approve($id)
    {
        $event = Event::findOrFail($id);
        
        $event->status = 'approved';
        $event->approved_at = now();
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
