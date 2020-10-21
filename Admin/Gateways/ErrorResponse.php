<?php

namespace SmsNotifier\Admin\Gateways;

class ErrorResponse
{
    /**
     * Get response
     * @param object $response
     * @param boolean|string $message
     * @return array $data
     */
    public function getResponse($status, $message = false)
    {
        $response = [];
        if ($status) {
            $response['status'] = true;
            $response['error'] = false;
        } else {
            $response['status'] = false;
            $response['error'] = $message ? $message : true;
        }
        return $response;
    }

    /**
     * Get error
     * @param object $e - exception error
     * @return array $error
     */
    public function getError($e)
    {
        $error = [];
        $error['status'] = false;
        $error['error'] = $e->getMessage();
        return $error;
    }
}
