<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'base_price',
        'features_list',
        'revision_quota',
        'estimated_days',
        'icon',
        'badge',
        'is_featured',
    ];

    protected $casts = [
        'features_list' => 'array',
        'base_price' => 'decimal:2',
        'is_featured' => 'boolean',
    ];

    public function getFeaturesAttribute()
    {
        if (is_array($this->features_list)) {
            return $this->features_list;
        }
        return json_decode($this->features_list ?? '[]', true) ?: [];
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'package_id');
    }
}
