<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'title',
        'stage_number', // 1 to 5
        'description',
        'staging_url',
        'status', // LOCKED, ACTIVE, IN_REVIEW, APPROVED
        'approved_at',
        'approved_ip',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'stage_number' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function revisionTickets()
    {
        return $this->hasMany(RevisionTicket::class, 'milestone_id');
    }
}
