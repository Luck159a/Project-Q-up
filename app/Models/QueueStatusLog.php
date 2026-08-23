<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QueueStatusLog extends Model
{
    protected $fillable = [
        'queue_id',
        'from_status',
        'to_status',
        'changed_by',
        'note',
    ];

    public function queue(): BelongsTo
    {
        return $this->belongsTo(Queue::class, 'queue_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}