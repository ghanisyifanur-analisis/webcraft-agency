<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'client_id',
        'package_id',
        'assigned_pm_id',
        'assigned_dev_id',
        'total_amount',
        'status', // WAITING_DP, BRIEF_PHASE, IN_PROGRESS, STAGING_REVIEW, COMPLETED, CANCELLED
        'start_date',
        'target_completion_date',
        'revisions_used',
        'admin_notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'start_date' => 'date',
        'target_completion_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function package()
    {
        return $this->belongsTo(ServicePackage::class, 'package_id');
    }

    public function assignedPm()
    {
        return $this->belongsTo(User::class, 'assigned_pm_id');
    }

    public function assignedDev()
    {
        return $this->belongsTo(User::class, 'assigned_dev_id');
    }

    public function addons()
    {
        return $this->hasMany(OrderAddon::class);
    }

    public function brief()
    {
        return $this->hasOne(BriefSubmission::class);
    }

    public function milestones()
    {
        return $this->hasMany(ProjectMilestone::class)->orderBy('stage_number');
    }

    public function revisionTickets()
    {
        return $this->hasMany(RevisionTicket::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function credentialVaults()
    {
        return $this->hasMany(CredentialVault::class);
    }

    public function financialLogs()
    {
        return $this->hasMany(FinancialLog::class);
    }

    // Termin 1 (DP 50%) Invoice
    public function dpInvoice()
    {
        return $this->hasOne(Invoice::class)->where('term_type', 'TERM_1_DP');
    }

    // Termin 2 (Settlement 50%) Invoice
    public function settlementInvoice()
    {
        return $this->hasOne(Invoice::class)->where('term_type', 'TERM_2_SETTLEMENT');
    }

    public function isDpPaid(): bool
    {
        $dp = $this->dpInvoice()->first();
        return $dp && $dp->status === 'PAID';
    }

    public function isFullyPaid(): bool
    {
        $unpaidCount = $this->invoices()->where('status', '!=', 'PAID')->count();
        return $unpaidCount === 0 && $this->invoices()->count() >= 2;
    }
}
