<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Names for the tablet's "escalation owner" box to suggest from. Deliberately
 * narrow: a name and a role, nothing personal. With no search term it returns
 * the whole list so the app can hold it for use offline.
 */
class EmployeeLookupController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));

        $employees = Employee::query()
            ->when($term !== '', function ($query) use ($term) {
                $query->where(function ($q) use ($term) {
                    $q->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('employee_number', 'like', "%{$term}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"]);
                });
            })
            ->where(function ($query) {
                $query->whereNull('status')->orWhere('status', '!=', 'inactive');
            })
            ->orderBy('first_name')->orderBy('last_name')
            ->limit($term === '' ? 200 : 25)
            ->get(['id', 'first_name', 'last_name', 'position', 'department']);

        return response()->json([
            'success' => true,
            'data' => $employees->map(fn ($employee) => [
                'id' => $employee->id,
                'name' => trim($employee->first_name.' '.$employee->last_name),
                'role' => $employee->position ?: $employee->department,
            ])->values(),
        ]);
    }
}
