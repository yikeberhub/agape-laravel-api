<?php

if (!function_exists('jsonResponse')) {
    function jsonResponse($success, $message, $data = null, $status = 200, $errors = null)
    {
        $response = [
            'success' => $success,
            'message' => $message,
        ];

        if ($success) {
            $response['data'] = $data;
        } else {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }
}