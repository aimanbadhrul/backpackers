<?php

namespace App\Models;

use App\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model
{
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'applications';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
        'event_id', 'user_id', 'status', 'notes', 'full_name', 'email', 'phone',
        'emergency_contact_name', 'emergency_contact_phone', 'submission_date',
        'approval_date', 'rejection_reason', 'payment_status', 'group', 'special_requests'
    ];
    
    // protected $hidden = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    public function user()
    {
    return $this->belongsTo(\App\Models\User::class);
    }

    public function approveButton()
    {
        if ($this->status !== 'pending') {
            return ''; // No button if already approved/rejected
        }
    
        $approveUrl = route('admin.application.approve', $this->id);
    

        return '<span class="float-end"><a href="' . $approveUrl . '" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Approve">
        <i class="la la-check"></i>Approve
    </a></span>';
    }
    
    public function rejectButton()
    {
        if ($this->status !== 'pending') {
            return ''; // No button if already approved/rejected
        }
    
        $rejectUrl = route('admin.application.reject', $this->id);
    

        return '<span class="float-end"><a href="' . $rejectUrl . '" class="ms-2 btn btn-sm  btn-danger" data-bs-toggle="tooltip" title="Reject">
        <i class="la la-times"></i>Reject
    </a></span>';
    }
    

    /*
    |--------------------------------------------------------------------------
    
    | RELATIONS
    |--------------------------------------------------------------------------
    */

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
