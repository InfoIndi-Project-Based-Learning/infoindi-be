<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    public function success(mixed $data, string $message = "Success",int $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    public function created(mixed $data, string $message = "Created", int $code = 201)
    {
        return $this->success($data, $message, 201);
    }

    public function paginated(LengthAwarePaginator $paginator, $message = "Success", $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $paginator->items(),
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

    public function error(string $message, int $code = 400, $data = null)
    {
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