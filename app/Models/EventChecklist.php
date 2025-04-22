<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventChecklist extends Model
{
    protected $table = 'event_checklist'; // double check this
    protected $fillable = [
        'event_id',
        'item_name',
        'category',
        'image',
        'quantity',
        'status',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
