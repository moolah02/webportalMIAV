<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\PosTerminal;
use Illuminate\Auth\Access\Response;

class PosTerminalPolicy
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
    public function view(Employee $employee, PosTerminal $posTerminal): bool
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
    public function update(Employee $employee, PosTerminal $posTerminal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Employee $employee, PosTerminal $posTerminal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Employee $employee, PosTerminal $posTerminal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Employee $employee, PosTerminal $posTerminal): bool
    {
        return false;
    }
}
