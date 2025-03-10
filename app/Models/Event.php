<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    }

    public static function getStatuses()
{
    return [
        'draft' => 'Draft',
        'submitted' => 'Submitted',
        // 'pending' => 'Pending Approval',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];
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
