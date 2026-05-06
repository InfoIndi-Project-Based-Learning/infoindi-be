<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


trait HasQuery
{
    public function applySearch(
        ?Builder $query,
        ?string $search,
        array $columns
        ): Builder {
            if(!$search) return $query;

            $query->where(function($q) use ($search, $columns){
                foreach($columns as $column){
                    $q->orWhere($column, 'LIKE', "%$search%");
                }
            });

            return $query;
        }

    public function applySort(
        Builder $query,
        Request $request,
        string $defaultSearch = 'created_at',
        string $defaultOrder = 'desc'
    ): Builder {
        $sortBy = $request->get('sort_by') ?? $defaultSearch;
        $sortOrder = $request->get('sort_order') ?? $defaultOrder;

        return $query->orderBy($sortBy, $sortOrder);
    }

    public function applyFilter(
        Builder $query,
        Request $request,
        array $filters
    ): Builder {
        foreach ($filters as $filter){
            $value = $request->get($filter);
            if(!is_null($value)){
                $query->where($filter, $value);
            }
        }
        return $query;
    }

    public function paginate(
        Builder $query,
        Request $request,
        int $defaultPerPage = 15
    ): LengthAwarePaginator {
        $perPage = $request->get('per_page', $defaultPerPage);
        return $query->paginate($perPage);
    }
}