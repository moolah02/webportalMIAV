<?php

// ==============================================
// 1. DEPARTMENT MODEL
// File: app/Models/Department.php
// ==============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    // Relationships
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function assets()
    {
        return $this->hasMany(InternalAsset::class);
    }

    public function assetRequests()
    {
        return $this->hasMany(AssetRequest::class);
    }

    public function businessLicenses()
    {
        return $this->hasMany(BusinessLicense::class, 'responsible_department_id');
    }
}