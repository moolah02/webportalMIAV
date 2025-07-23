<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'permissions'
    ];

    protected $casts = [
        'permissions' => 'array'
    ];

    // CHANGE THIS: Use hasMany instead of belongsToMany
    public function employees()
    {
        return $this->hasMany(Employee::class); // NOT belongsToMany
    }

    // Helper methods (keep these the same)
    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions ?? []) || in_array('all', $this->permissions ?? []);
    }

    public function addPermission($permission)
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->permissions = $permissions;
        }
        return $this;
    }

    public function removePermission($permission)
    {
        $permissions = $this->permissions ?? [];
        $this->permissions = array_values(array_filter($permissions, function($p) use ($permission) {
            return $p !== $permission;
        }));
        return $this;
    }
}