<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends BaseApiController
{
    private SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function search(Request $request)
    {
        $query = $request->query('q');

        if (empty($query) || strlen(trim($query)) < 1) {
            return $this->success([
                'posts' => [],
                'categories' => [],
                'users' => []
            ], 'Empty query.');
        }

        $results = $this->searchService->search(trim($query));

        return $this->success($results, 'Search results fetched successfully.');
    }
}
