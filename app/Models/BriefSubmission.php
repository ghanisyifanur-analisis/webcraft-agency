<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BriefSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'brand_name',
        'tagline_description',
        'color_palette',
        'target_audience',
        'competitor_references',
        'logo_file_path',
        'supporting_assets_url',
        'feature_notes',
        'status', // SUBMITTED, NEED_REVISION, ACCEPTED
        'pm_feedback',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
