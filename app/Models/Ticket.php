<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'technician_id',
        'pos_terminal_id',
        'client_id',
        'visit_id',
        'issue_type',
        'priority',
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
        'resolved_at' => 'datetime'
    ];

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

    // Generate unique ticket ID
    public static function generateTicketId()
    {
        $prefix = 'TKT-' . date('Ymd') . '-';
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
}