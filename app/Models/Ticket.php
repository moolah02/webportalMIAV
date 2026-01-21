<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'ticket_type',
        'assignment_type',
        'mobile_created',
        'offline_sync_id',
        'technician_id',
        'pos_terminal_id',
        'client_id',
        'visit_id',
        'issue_type',
        'priority',
        'estimated_resolution_time',
        'title',
        'description',
        'status',
        'assigned_to',
        'resolution',
        'attachments',
        'resolved_at'
    ];

    protected $casts = [
        'attachments' => 'array',
        'resolved_at' => 'datetime',
        'mobile_created' => 'boolean',
        'estimated_resolution_time' => 'integer',
        'ticket_type' => 'string',
        'assignment_type' => 'string'
    ];

    // Relationships
    public function technician()
    {
        return $this->belongsTo(Employee::class, 'technician_id');
    }

    public function posTerminal()
    {
        return $this->belongsTo(PosTerminal::class, 'pos_terminal_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function visit()
    {
        return $this->belongsTo(TechnicianVisit::class, 'visit_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function steps()
    {
        return $this->hasMany(TicketStep::class)->orderBy('step_number');
    }

    public function currentStep()
    {
        return $this->steps()->where('status', 'in_progress')->first();
    }

    // Scopes for easy filtering
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByIssueType($query, $issueType)
    {
        return $query->where('issue_type', $issueType);
    }

    public function scopeByTicketType($query, $ticketType)
    {
        return $query->where('ticket_type', $ticketType);
    }

    public function scopeByAssignmentType($query, $assignmentType)
    {
        return $query->where('assignment_type', $assignmentType);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'pending']);
    }

    public function scopeCritical($query)
    {
        return $query->where('priority', 'critical');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopePosTerminalTickets($query)
    {
        return $query->where('ticket_type', 'pos_terminal');
    }

    public function scopeInternalTickets($query)
    {
        return $query->where('ticket_type', 'internal');
    }

    public function scopePublicTickets($query)
    {
        return $query->where('assignment_type', 'public');
    }

    public function scopeDirectTickets($query)
    {
        return $query->where('assignment_type', 'direct');
    }

    // Generate unique ticket ID
    public static function generateTicketId()
    {
        $prefix = 'TKT-' . date('Y') . '-';
        $lastTicket = self::where('ticket_id', 'like', $prefix . '%')
            ->orderBy('ticket_id', 'desc')
            ->first();

        if ($lastTicket) {
            $lastNumber = intval(substr($lastTicket->ticket_id, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $prefix . $newNumber;
    }

    // Auto-generate ticket_id when creating
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_id)) {
                $ticket->ticket_id = self::generateTicketId();
            }
        });
    }

    // Helper methods
    public function getStatusBadgeClassAttribute()
    {
        return 'status-' . str_replace('_', '-', $this->status);
    }

    public function getPriorityBadgeClassAttribute()
    {
        return 'priority-' . $this->priority;
    }

    public function getIssueTypeBadgeClassAttribute()
    {
        return 'issue-' . $this->issue_type;
    }

    public function getFormattedIssueTypeAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->issue_type));
    }

    public function getFormattedStatusAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function isOverdue()
    {
        if (!$this->estimated_resolution_time || $this->status === 'resolved' || $this->status === 'closed') {
            return false;
        }

        $estimatedCompletion = $this->created_at->addMinutes($this->estimated_resolution_time);
        return now()->gt($estimatedCompletion);
    }
}
