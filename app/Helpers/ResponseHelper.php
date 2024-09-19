<?php

namespace App\Helpers;

class ResponseHelper
{
    /**
     * Devuelve una respuesta de éxito.
     */
    public static function success($message, $data = [], $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Devuelve una respuesta de error.
     */
    public static function error($message, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }
}
