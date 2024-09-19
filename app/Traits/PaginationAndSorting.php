<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait PaginationAndSorting
{
    /**
     * Aplica la paginación y el ordenamiento a una consulta.
     */
    public function applyPaginationAndSorting($query, Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');

        return $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
    }
}
