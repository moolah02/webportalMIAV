<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'executive_summary',
        'key_achievements',
        'challenges_overcome',
        'lessons_learned',
        'quality_score',
        'client_satisfaction',
        'issues_found',
        'recommendations',
        'additional_notes',
        'completed_by'
    ];

    protected $casts = [
        'quality_score' => 'integer',
        'client_satisfaction' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function completedBy()
    {
        return $this->belongsTo(Employee::class, 'completed_by');
    }
}
