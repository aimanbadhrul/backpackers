<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use CrudTrait;
    use HasFactory;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'events';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    protected $casts = [
        'cost' => 'decimal:2',
        'itinerary' => 'array',
        'item_checklist' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
    protected $fillable = ['title', 'location', 'description', 'start_date', 'end_date', 'max_participants', 'cost', 'status', 'itinerary', 'checklist', 'additional_info', 'created_by', 'submitted_at', 'approved_at','completed_at',];


    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();
    
        static::creating(function ($event) {
            $event->created_by = backpack_auth()->check() ? backpack_auth()->id() : null;
        });

        static::created(function ($event) {
            Cache::forget('event_dashboard_' . $event->id);
        });
    }

//Status Functions-------------------------------------------------

    public static function getStatuses()
    {
        return collect([
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            // 'pending' => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'completed' => 'Completed'
        ])->toArray();
    }

    public function getStatusBadge()
    {
        $colors = [
            'draft' => 'secondary',   // Gray
            'submitted' => 'info',    // Blue
            // 'pending' => 'warning', // Yellow
            'approved' => 'success',  // Green
            'rejected' => 'danger',   // Red
            'completed' => 'dark'
        ];

        $badgeColor = $colors[$this->status] ?? 'dark'; // Default to dark if status is unknown

        return '<span class="badge bg-' . $badgeColor . '">' . ucfirst($this->status) . '</span>';
    }


//Activity Log Functions-------------------------------------------------

    public static function booted()
    {
        static::updating(function ($event) {
            if ($event->isDirty('status')) {
                switch ($event->status) {
                    case 'submitted':
                        $event->submitted_at = now();
                        break;
                    case 'approved':
                        $event->approved_at = now();
                        break;
                    case 'completed':
                        $event->completed_at = now();
                        break;
                }
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'title', 'start_date', 'end_date', 'location','max_participants','cost','itinerary', 'item_checklist', 'additional_info', 'created_by'])
            ->logOnlyDirty()
            ->useLogName('event')
            ->setDescriptionForEvent(function (string $eventName) {
                // Track which attributes have changed
                $changes = [];
    
                if ($this->isDirty('status')) {
                    $changes[] = 'Status';
                }
                if ($this->isDirty('title')) {
                    $changes[] = 'Title';
                }
                if ($this->isDirty('start_date')) {
                    $changes[] = 'Start date';
                }
                if ($this->isDirty('end_date')) {
                    $changes[] = 'End date';
                }
                if ($this->isDirty('location')) {
                    $changes[] = 'Location';
                }
                if ($this->isDirty('max_participants')) {
                    $changes[] = 'Max participants';
                }                
                if ($this->isDirty('cost')) {
                    $changes[] = 'Cost';
                }
                if ($this->isDirty('itinerary')) {
                    $changes[] = 'Itinerary';
                }
                if ($this->isDirty('item_checklist')) {
                    $changes[] = 'Item checklist';
                }
                if ($this->isDirty('additional_info')) {
                    $changes[] = 'Additional information';
                }
                if ($this->isDirty('created_by')) {
                    $changes[] = 'created by';
                }
    
                if (empty($changes)) {
                    return "Event has been {$eventName}";
                }
    
                return "Event has been {$eventName} (" . implode(', ', $changes) . ")";
            });
    }
    

    public function approvalButtons()
    {
    if ($this->status !== 'submitted') {
        return ''; // Only show buttons for submitted events
    }

    $approveUrl = route('admin.submitted-event.approve', $this->id);
    $rejectUrl = route('admin.submitted-event.reject', $this->id);

    return '
        <div class="text-end">
            <a href="' . $approveUrl . '" class="btn btn-sm btn-success">
                <i class="la la-check"></i> Approve
            </a>
            <a href="' . $rejectUrl . '" class="btn btn-sm btn-danger">
                <i class="la la-times"></i> Reject
            </a>
        </div>';
}

    public function approveButton()
    {
    if ($this->status !== 'submitted') {
        return ''; // No button if already approved/rejected
    }

    $approveUrl = route('admin.submitted-event.approve', $this->id);


    return '<span class="float-end"><a href="' . $approveUrl . '" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Approve">
    <i class="la la-check"></i>Approve</a></span>';
    }

    public function rejectButton()
    {
    if ($this->status !== 'submitted') {
        return ''; // No button if already approved/rejected
    }

    $rejectUrl = route('admin.submitted-event.reject', $this->id);


    return '<span class="float-end"><a href="' . $rejectUrl . '" class="ms-2 btn btn-sm  btn-danger" data-bs-toggle="tooltip" title="Reject">
    <i class="la la-times"></i>Reject
</a></span>';
}

public function approveButtonShow()
{
if ($this->status !== 'submitted') {
    return ''; // No button if already approved/rejected
}

$approveUrl = route('admin.submitted-event.approve', $this->id);


return '<a href="' . $approveUrl . '" class="btn btn-sm btn-success" title="Approve">
<i class="la la-check"></i>Approve
</a>';
}
public function rejectButtonShow()
{
    if ($this->status !== 'submitted') {
        return ''; // No button if already approved/rejected
    }

    $rejectUrl = route('admin.submitted-event.reject', $this->id);

    return '<a href="' . $rejectUrl . '" class="btn btn-sm btn-danger" title="Reject">
                <i class="la la-times"></i>Reject
            </a>';
}

public function submitButton()
{
    if (backpack_user()->id !== $this->created_by && !backpack_user()->hasRole('Superadmin')) {
        return ''; // Hide button if not the creator and not a Superadmin
    }

    if (!in_array($this->status, ['draft', 'rejected'])) {
        return ''; // No button if not draft or rejected
    }

    $submitUrl = route('admin.event.submit', $this->id);


    return '<span class="float-end">
                <a href="' . $submitUrl . '" class="btn btn-sm btn-success" style="width: 70px; text-align: center;" 
                   onclick="return confirm(\'Are you sure you want to submit this event for approval?\')" 
                   data-bs-toggle="tooltip" title="Submit">
                    <i class="la la-check"></i> Submit
                </a>
            </span>';
}

public function canEdit()
{
    return backpack_user()->hasRole('Superadmin') ||
           (backpack_user()->id === $this->created_by && in_array($this->status, ['draft', 'rejected']));
}

public function canSubmitForApproval()
{
    return in_array($this->status, ['','draft', 'rejected']);
}

public function getDisplayTitleAttribute()
{
    return $this->title . ' (' . $this->start_date->format('M d, Y') . ' - ' . $this->end_date->format('M d, Y') . ')';
}


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function checklistItems()
    {
        return $this->hasMany(EventChecklist::class);
    }

    public function approvedParticipants()
    {
        return $this->hasMany(Application::class, 'event_id')
                ->where('status', 'approved');
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public $timestamps = true;

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
