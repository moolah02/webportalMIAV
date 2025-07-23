<?php

// 8. TECHNICIAN MODEL
// File: app/Models/Technician.php
// ==============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technician extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'employee_code',
        'specializations',
        'regions',
        'availability_status',
        'phone',
        'email',
        'hire_date',
    ];

    protected $casts = [
        'specializations' => 'array',
        'regions' => 'array',
        'hire_date' => 'date',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function jobAssignments()
    {
        return $this->hasMany(JobAssignment::class);
    }

    public function serviceReports()
    {
        return $this->hasMany(ServiceReport::class);
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('availability_status', 'available');
    }

    public function scopeBusy($query)
    {
        return $query->where('availability_status', 'busy');
    }

    public function scopeInRegion($query, $region)
    {
        return $query->whereJsonContains('regions', $region);
    }

    // Helper methods
    public function isAvailable()
    {
        return $this->availability_status === 'available';
    }

    public function getFullNameAttribute()
    {
        return $this->employee ? $this->employee->full_name : 'Unknown';
    }

    public function hasSpecialization($specialization)
    {
        return in_array($specialization, $this->specializations ?? []);
    }

    public function coversRegion($region)
    {
        return in_array($region, $this->regions ?? []);
    }

    public function getTodaysJobsCountAttribute()
    {
        return $this->jobAssignments()
            ->whereDate('scheduled_date', today())
            ->count();
    }

    public function getPendingJobsCountAttribute()
    {
        return $this->jobAssignments()
            ->whereIn('status', ['assigned', 'pending'])
            ->count();
    }
}
