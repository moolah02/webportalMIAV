<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketStep extends Model
{
    use HasFactory;

    protected $table = 'ticket_steps';

    protected $fillable = [
        'ticket_id',
        'employee_id',
        'step_number',
        'status',
        'description',
        'notes',
        'resolution_notes',
        'transferred_reason',
        'transferred_to',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function transferredToEmployee()
    {
        return $this->belongsTo(Employee::class, 'transferred_to');
    }

    // Scopes
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeTransferred($query)
    {
        return $query->where('status', 'transferred');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    // Methods
    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
        return $this;
    }

    public function transferTo(Employee $employee, string $reason)
    {
        $this->status = 'transferred';
        $this->transferred_to = $employee->id;
        $this->transferred_reason = $reason;
        $this->completed_at = now();
        $this->save();

        // Create new step for the next person
        return TicketStep::create([
            'ticket_id' => $this->ticket_id,
            'employee_id' => $employee->id,
            'step_number' => $this->step_number + 1,
            'status' => 'in_progress',
            'description' => "Continued from Step {$this->step_number}: {$reason}",
        ]);
    }

    public function markAsResolved()
    {
        $this->status = 'resolved';
        $this->completed_at = now();
        $this->save();
        return $this;
    }

    // Get audit trail summary
    public function getAuditTrail()
    {
        return [
            'step_number' => $this->step_number,
            'employee' => $this->employee ? $this->employee->first_name . ' ' . $this->employee->last_name : 'Unknown',
            'status' => ucfirst($this->status),
            'work_done' => $this->description,
            'notes' => $this->notes,
            'resolution' => $this->resolution_notes,
            'transferred_to' => $this->transferredToEmployee ? $this->transferredToEmployee->first_name . ' ' . $this->transferredToEmployee->last_name : null,
            'transfer_reason' => $this->transferred_reason,
            'completed_at' => $this->completed_at?->format('M d, Y H:i A'),
        ];
    }
}
