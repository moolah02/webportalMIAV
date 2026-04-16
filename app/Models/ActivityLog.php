<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    protected $fillable = [
        'employee_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Log an activity.
     */
    public static function log(string $action, string $description, $model = null, array $oldValues = [], array $newValues = []): self
    {
        return self::create([
            'employee_id' => auth()->id(),
            'action'      => $action,
            'model_type'  => $model ? class_basename($model) : null,
            'model_id'    => $model ? $model->id : null,
            'description' => $description,
            'old_values'  => $oldValues ?: null,
            'new_values'  => $newValues ?: null,
            'ip_address'  => Request::ip(),
            'user_agent'  => substr(Request::userAgent() ?? '', 0, 255),
        ]);
    }
}
