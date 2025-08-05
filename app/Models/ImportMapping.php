<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportMapping extends Model
{
    protected $fillable = [
        'mapping_name',
        'client_id', 
        'column_mappings',
        'description',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'column_mappings' => 'array',
        'is_active' => 'boolean'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    // Get column mapping for a specific field
    public function getColumnIndex(string $field): ?int
    {
        return $this->column_mappings[$field] ?? null;
    }

    // Get all available mappings
    public static function getActiveMappings()
    {
        return self::where('is_active', true)->get();
    }
}