<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\Technician;
use Illuminate\Auth\Access\Response;

class TechnicianPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Employee $employee): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Employee $employee, Technician $technician): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Employee $employee): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Employee $employee, Technician $technician): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Employee $employee, Technician $technician): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Employee $employee, Technician $technician): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Employee $employee, Technician $technician): bool
    {
        return false;
    }
}
