<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AssetAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'employee_id',
        'quantity_assigned',
        'assignment_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'condition_when_assigned',
        'condition_when_returned',
        'assigned_by',
        'returned_to',
        'assignment_notes',
        'return_notes',
        'asset_request_id',
    ];

    protected $casts = [
        'assignment_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
    ];

    // Relationships
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(Employee::class, 'assigned_by');
    }

    public function returnedTo()
    {
        return $this->belongsTo(Employee::class, 'returned_to');
    }

    public function assetRequest()
    {
        return $this->belongsTo(AssetRequest::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'assigned')
                    ->where('expected_return_date', '<', now())
                    ->whereNull('actual_return_date');
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForAsset($query, $assetId)
    {
        return $query->where('asset_id', $assetId);
    }

    // Accessors & Helpers
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'assigned' => 'status-active',
            'returned' => 'status-pending',
            'lost' => 'status-offline',
            'damaged' => 'status-offline',
            'transferred' => 'status-pending',
            default => 'status-pending'
        };
    }

    public function getDaysAssignedAttribute()
    {
        $endDate = $this->actual_return_date ?? now();
        return $this->assignment_date->diffInDays($endDate);
    }

    public function isOverdue()
    {
        return $this->status === 'assigned' && 
               $this->expected_return_date && 
               $this->expected_return_date->isPast() && 
               !$this->actual_return_date;
    }

    public function getDaysOverdueAttribute()
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return $this->expected_return_date->diffInDays(now());
    }

    public function canBeReturned()
    {
        return $this->status === 'assigned';
    }

    public function canBeTransferred()
    {
        return $this->status === 'assigned';
    }

    // Static methods
    public static function getConditionOptions()
    {
        return [
            'new' => 'New',
            'good' => 'Good',
            'fair' => 'Fair',
            'poor' => 'Poor'
        ];
    }

    public static function getStatusOptions()
    {
        return [
            'assigned' => 'Assigned',
            'returned' => 'Returned',
            'lost' => 'Lost',
            'damaged' => 'Damaged',
            'transferred' => 'Transferred'
        ];
    }

    // Create assignment history entry when status changes
    protected static function boot()
    {
        parent::boot();

        static::created(function ($assignment) {
            // Update asset assigned quantity
            $assignment->asset->increment('assigned_quantity', $assignment->quantity_assigned);
        });

        static::updated(function ($assignment) {
            // If status changed to returned, decrement assigned quantity
            if ($assignment->isDirty('status') && $assignment->status === 'returned') {
                $assignment->asset->decrement('assigned_quantity', $assignment->quantity_assigned);
            }
        });

        static::deleted(function ($assignment) {
            // If assignment is deleted and was active, update asset quantity
            if ($assignment->status === 'assigned') {
                $assignment->asset->decrement('assigned_quantity', $assignment->quantity_assigned);
            }
        });
    }

    // Get assignment statistics
    public static function getStats()
    {
        return [
            'total_assignments' => self::count(),
            'active_assignments' => self::where('status', 'assigned')->count(),
            'overdue_assignments' => self::overdue()->count(),
            'returned_this_month' => self::where('status', 'returned')
                ->whereMonth('actual_return_date', now()->month)
                ->whereYear('actual_return_date', now()->year)
                ->count(),
        ];
    }
}