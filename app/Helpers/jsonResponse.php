<?php

if (!function_exists('jsonResponse')) {
    function jsonResponse($success, $message, $data = null, $status = 200, $errors = null)
    {
        $response = [
            'success' => $success,
            'message' => $message,
        ];

        $response['data'] = $success ? $data : [];

        if (!$success && $errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }
}