<?php

namespace App\Http\Controllers\Admin\Events;

use App\Models\Event;
use App\Models\Application;
use App\Http\Requests\EventRequest;
use App\Http\Requests\ApprovedEventRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ApprovedEventCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ApprovedEventCrudController extends EventCrudController
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
        CRUD::setRoute(config('backpack.base.route_prefix') . '/approved-event');
        CRUD::setEntityNameStrings('approved event', 'approved events');

        CRUD::addClause('where', 'status', 'approved');
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
        parent::setupListOperation();

        CRUD::removeButton('create');
        if ($user->hasRole('Superadmin')) {
            CRUD::addButton('line', 'update', 'view', 'crud::buttons.update');
            CRUD::addButton('line', 'delete', 'view', 'crud::buttons.delete');
            }

        CRUD::addColumn([
            'name' => 'dashboard_link',
            'label' => 'Event Dashboard',
            'type' => 'custom_html',
            'value' => function ($entry) {
                return '<a href="' . route('admin.event-dashboard', $entry->id) . '" class="btn btn-sm btn-primary">View Dashboard</a>';
            },
            'escaped' => false,
        ]);
    }

    protected function setupCreateOperation()
    {
        parent::setupCreateOperation();
    }

    protected function setupUpdateOperation()
    {
        $user = backpack_user();
        CRUD::setValidation(EventRequest::class);
        CRUD::setHeading($this->crud->getCurrentEntry()->title ?? 'Edit Approved Event');
        CRUD::setSubheading('Details added here will be displayed on the Event Dashboard');

        CRUD::field('title')
        ->attributes(['readonly' => 'readonly'])
        ->tab('Details');

        CRUD::field('description')
        ->attributes(['readonly' => 'readonly'])
        ->tab('Details');

        CRUD::field('location')
        ->attributes(['readonly' => 'readonly'])
        ->tab('Details');

        CRUD::field('start_date')
        ->attributes(['readonly' => 'readonly'])
        ->wrapper(['class' => 'form-group col-md-3'])
        ->tab('Details');

        CRUD::field('end_date')
        ->attributes(['readonly' => 'readonly'])
        ->wrapper(['class' => 'form-group col-md-3'])
        ->tab('Details');

        CRUD::field('max_participants')
        ->attributes(['readonly' => 'readonly'])
        ->wrapper(['class' => 'form-group col-md-3'])
        ->tab('Details');

        CRUD::field('cost')
        ->label('Cost (RM)')
        ->attributes(['readonly' => 'readonly'])
        ->wrapper(['class' => 'form-group col-md-3'])
        ->tab('Details');

        if ($user->hasRole('Superadmin')) {
            CRUD::addField([
                'name' => 'status',
                'label' => 'Status',
                'type' => 'select_from_array',
                'options' => Event::getStatuses(),
                'allows_null' => false,
                'default' => 'draft',
                'tab' => 'Details'
            ]);
        }

        //Participant List Tab
        $event = $this->crud->getCurrentEntry();

        CRUD::addField([
            'name'  => 'approved_participants',
            'type'  => 'custom_html',
            'tab'   => 'Participant List',
            'value' => $this->getApprovedParticipantsHtml($event),
        ]);

        CRUD::addField([
            'name' => 'itinerary',
            'label' => 'Event Itinerary',
            'type' => 'summernote',
            'tab' => 'Itinerary',
            'options' => [
                'height' => 300,
            ],
        ]);

        CRUD::addField([
            'name' => 'checklist',
            'label' => 'Item Checklist',
            'type' => 'summernote',
            'tab' => 'Item Checklist',
            'options' => [
                'height' => 300,
            ],
        ]);

        CRUD::addField([
            'name' => 'additional_info',
            'label' => 'Additional Information',
            'type' => 'summernote',
            'tab' => 'Additional Information',
            'options' => [
                'height' => 300,
            ],
        ]);
    }
    
    protected function setupShowOperation()
    {
        parent::setupShowOperation();
    }

    private function getApprovedParticipantsHtml($event)
    {
        $approvedParticipants = $event->approvedParticipants;
    
        $html = '<h5><strong>Participants</strong></h5>';
    
        if ($approvedParticipants->isEmpty()) {
            $html .= '<p>No approved participants yet.</p>';
        } else {
            $html .= '
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($approvedParticipants as $index => $participant) {
                $html .= '
                            <tr>
                                <td>' . ($index + 1) . '</td>
                                <td>' . e($participant->full_name) . '</td>
                                <td>' . e($participant->email) . '</td>
                                <td>' . e($participant->phone) . '</td>
                            </tr>';
            }
            $html .= '
                        </tbody>
                    </table>
                </div>';
        }
    
        return $html;
    }  
}






        //Item Checklist Template for now, before PRO version

        // CRUD::addField([
        //     'name'  => 'checklist_item_1_name',
        //     'label' => 'Item 1 Name',
        //     'type'  => 'text',
        //     'tab'   => 'Item Checklist',
        //     'wrapperAttributes' => ['class' => 'form-group col-md-4'], // 4/12 width
        // ]);
        
        // CRUD::addField([
        //     'name'    => 'checklist_item_1_category',
        //     'label'   => 'Item 1 Category',
        //     'type'    => 'select_from_array',
        //     'options' => ['Clothing' => 'Clothing', 'Gear' => 'Gear', 'Food' => 'Food', 'Misc' => 'Misc'],
        //     'tab'     => 'Item Checklist',
        //     'wrapperAttributes' => ['class' => 'form-group col-md-2'],
        // ]);
        
        // CRUD::addField([
        //     'name'   => 'checklist_item_1_image',
        //     'label'  => 'Item 1 Image',
        //     'type'   => 'upload',
        //     'upload' => true,
        //     'tab'    => 'Item Checklist',
        //     'wrapperAttributes' => ['class' => 'form-group col-md-2'],
        // ]);
        
        // CRUD::addField([
        //     'name'  => 'checklist_item_1_quantity',
        //     'label' => 'Item 1 Quantity',
        //     'type'  => 'number',
        //     'tab'   => 'Item Checklist',
        //     'wrapperAttributes' => ['class' => 'form-group col-md-2'],
        // ]);
        
        // CRUD::addField([
        //     'name'    => 'checklist_item_1_status',
        //     'label'   => 'Item 1 Status',
        //     'type'    => 'select_from_array',
        //     'options' => ['required' => 'Required', 'optional' => 'Optional'],
        //     'tab'     => 'Item Checklist',
        //     'wrapperAttributes' => ['class' => 'form-group col-md-2'],
        // ]);
        
        
        // Repeat for checklist_item_2, checklist_item_3, etc.
        
        
        // CRUD::addField([
        //     'name' => 'checklistItems', // relationship name
        //     'label' => 'Item Checklist',
        //     'type' => 'repeatable',
        //     'fields' => [
        //         [
        //             'name'  => 'item_name',
        //             'label' => 'Item Name',
        //             'type'  => 'text',
        //         ],
        //         [
        //             'name' => 'category',
        //             'label' => 'Category',
        //             'type' => 'select_from_array',
        //             'options' => ['Clothing' => 'Clothing', 'Gear' => 'Gear', 'Food' => 'Food', 'Misc' => 'Misc'],
        //         ],
        //         [
        //             'name'  => 'image',
        //             'label' => 'Image',
        //             'type'  => 'upload',
        //             'upload' => true,
        //         ],
        //         [
        //             'name' => 'quantity',
        //             'label' => 'Quantity',
        //             'type' => 'number',
        //         ],
        //         [
        //             'name' => 'status',
        //             'label' => 'Status',
        //             'type' => 'select_from_array',
        //             'options' => ['required' => 'Required', 'optional' => 'Optional'],
        //         ],
        //     ],
        //     'new_item_label'  => 'Add Checklist Item',
        //     'tab'             => 'Item Checklist',
        // ]);

