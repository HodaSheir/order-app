<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\OTP;
use Illuminate\Support\Facades\Http;


if (!function_exists('apiResponse')) {
    /**
     * upload image in specific directory "storage"
     * @param $success
     * @param $message
     * @param null $data
     * @return json
     */
    function apiResponse($success, $message, $statusCode, $data = null, $meta_data=null)
    {
        $response =  [
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($response, $statusCode);
    }
}




