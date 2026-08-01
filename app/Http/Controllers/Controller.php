<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function successResponse(string $message, mixed $data = null, int $statusCode = 200)
    {
        return response()->json([
            'status_code' => $statusCode,
            'success'     => true,
            'data'        => $data,
            'message'     => $message,
        ], $statusCode);
    }

    protected function errorResponse(string $message, int $statusCode = 400)
    {
        return response()->json([
            'status_code' => $statusCode,
            'success'     => false,
            'message'     => $message,
        ], $statusCode);
    }
}
