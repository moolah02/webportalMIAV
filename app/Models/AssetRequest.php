<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'employee_id',
        'status',
        'priority',
        'business_justification',
        'needed_by_date',
        'delivery_instructions',
        'total_estimated_cost',
        'department',
        'cost_center',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejection_reason',
        'fulfilled_by',
        'fulfilled_at',
        'fulfillment_notes',
    ];

    protected $casts = [
        'needed_by_date' => 'date',
        'approved_at' => 'datetime',
        'fulfilled_at' => 'datetime',
        'total_estimated_cost' => 'decimal:2',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function fulfiller()
    {
        return $this->belongsTo(Employee::class, 'fulfilled_by');
    }

    public function items()
    {
        return $this->hasMany(AssetRequestItem::class);
    }
// In your AssetRequest model
public function getRouteKeyName()
{
    return 'id'; // or whatever column you want to use for route binding
}
    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeNeedsApproval($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    // Accessors & Helpers
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => 'status-pending',
            'pending' => 'status-pending',
            'approved' => 'status-active',
            'rejected' => 'status-offline',
            'fulfilled' => 'status-active',
            'cancelled' => 'status-offline',
            default => 'status-pending'
        };
    }

    public function getPriorityBadgeAttribute()
    {
        return match($this->priority) {
            'low' => 'status-active',
            'normal' => 'status-pending',
            'high' => 'status-pending',
            'urgent' => 'status-offline',
            default => 'status-pending'
        };
    }

    public function canBeApproved()
    {
        return $this->status === 'pending';
    }

    public function canBeRejected()
    {
        return $this->status === 'pending';
    }

    public function canBeFulfilled()
    {
        return $this->status === 'approved';
    }

    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity_requested');
    }

    public function recalculateTotal()
    {
        $total = $this->items->sum('total_price');
        $this->update(['total_estimated_cost' => $total]);
        return $total;
    }

    // Auto-generate request number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            if (!$request->request_number) {
                $request->request_number = static::generateRequestNumber();
            }
        });
    }

    public static function generateRequestNumber()
    {
        $year = now()->year;
        $lastRequest = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastRequest ?
            intval(substr($lastRequest->request_number, -3)) + 1 : 1;

        return 'REQ-' . $year . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}