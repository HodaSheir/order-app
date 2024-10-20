<?php

namespace App\Http\Controllers;

use App\Traits\ResponseUtil;
use Illuminate\Support\Facades\Response;


class AppBaseController extends Controller
{

    use ResponseUtil;

    public function sendResponse($result, $message)
    {
        return Response::json($this->makeResponse($message, $result));
    }

    public function sendError($error, $code = 200)
    {
        return Response::json($this->makeError($error), $code);
    }

    public function sendSuccess($message)
    {
        return Response::json([
            'success' => true,
            'message' => $message
        ], 200);
    }
}
