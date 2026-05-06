<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;


trait ApiResponse
{
    public function success(
        mixed $data, 
        string $message = "Success",
        int $code = 200
        ): JsonResponse {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function created(
        mixed $data, 
        string $message = "Created", 
        int $code = 201
        ): JsonResponse {
        return $this->success($data, $message, 201);
    }

    public function paginated(
        LengthAwarePaginator $paginator,
        $resource,  
        $message = "Success", 
        $code = 200
        ): JsonResponse {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $resource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem()
            ]
        ]);
    }

    public function error(
        string $message, 
        int $code = 400, 
        $data = null
        ): JsonResponse {
        $reponse = $data? [
            'status' => false,
            'message' => $message,
            'data' => $data
        ] : [
            'status' => false,
            'message' => $message
        ];

        return response()->json($reponse, $code);
    }
}