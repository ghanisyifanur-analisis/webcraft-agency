<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevisionTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'milestone_id',
        'ticket_number',
        'issue_description',
        'target_url',
        'attachment_url',
        'status', // open, approved_for_work, in_progress, resolved, rejected
        'developer_response',
        'is_paid_addon',
    ];

    protected $casts = [
        'is_paid_addon' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function milestone()
    {
        return $this->belongsTo(ProjectMilestone::class, 'milestone_id');
    }
}
