<?php


namespace App\Traits;


use Illuminate\Support\Facades\Response;

trait ResponseUtil
{

    /**
     * @param string $message
     * @param mixed $data
     *
     * @return array
     */
    public function makeResponse($message, $data)
    {
        return [
            'success' => true,
            'data' => $data,
            'message' => $message,
        ];
    }

    /**
     * @param string $message
     * @param array $data
     *
     * @return array
     */
    public function makeError($message, array $data = [])
    {
        $res = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($data)) {
            $res['data'] = $data;
        }

        return $res;
    }

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
