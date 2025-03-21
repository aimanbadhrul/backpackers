<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use CrudTrait;
    use HasFactory;

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
        'cost' => 'decimal:2', // Ensure Laravel correctly casts to decimal
    ];
    protected $fillable = ['title', 'location', 'description', 'start_date', 'end_date', 'max_participants', 'cost', 'status'];


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

    public static function getStatuses()
    {
        return collect([
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            // 'pending' => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
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
    ];

    $badgeColor = $colors[$this->status] ?? 'dark'; // Default to dark if status is unknown

    return '<span class="badge bg-' . $badgeColor . '">' . ucfirst($this->status) . '</span>';
}

    public function approvalButtons()
    {
    if ($this->status !== 'submitted') {
        return ''; // Only show buttons for submitted events
    }

    $approveUrl = route('admin.event-approval.approve', $this->id);
    $rejectUrl = route('admin.event-approval.reject', $this->id);

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

    $approveUrl = route('admin.event-approval.approve', $this->id);


    return '<span class="float-end"><a href="' . $approveUrl . '" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Approve">
    <i class="la la-check"></i>Approve
</a></span>';
}

    public function rejectButton()
    {
    if ($this->status !== 'submitted') {
        return ''; // No button if already approved/rejected
    }

    $rejectUrl = route('admin.event-approval.reject', $this->id);


    return '<span class="float-end"><a href="' . $rejectUrl . '" class="ms-2 btn btn-sm  btn-danger" data-bs-toggle="tooltip" title="Reject">
    <i class="la la-times"></i>Reject
</a></span>';
}

public function approveButtonShow()
{
if ($this->status !== 'submitted') {
    return ''; // No button if already approved/rejected
}

$approveUrl = route('admin.event-approval.approve', $this->id);


return '<a href="' . $approveUrl . '" class="btn btn-sm btn-success" title="Approve">
<i class="la la-check"></i>Approve
</a>';
}
public function rejectButtonShow()
{
    if ($this->status !== 'submitted') {
        return ''; // No button if already approved/rejected
    }

    $rejectUrl = route('admin.event-approval.reject', $this->id);

    return '<a href="' . $rejectUrl . '" class="btn btn-sm btn-danger" title="Reject">
                <i class="la la-times"></i>Reject
            </a>';
}

public function submitButton()
{
    if (backpack_user()->id !== $this->created_by) {
        return ''; // Hide button if not the creator
    }

    if (!in_array($this->status, ['draft', 'rejected'])) {
        return ''; // No button if not draft or rejected
    }

    $submitUrl = url('admin/event/' . $this->id . '/submit');

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
    return backpack_user()->hasRole('Event Leader') && in_array($this->status, ['draft', 'rejected']);
}

public function canSubmitForApproval()
{
    return backpack_user()->hasRole('Event Leader') && in_array($this->status, ['draft', 'rejected']);
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
