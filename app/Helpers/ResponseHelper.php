<?php

// Generate a JSON response with a 404(Not found) status code.
if (!function_exists('response404')) {
    function response404($message)
    {
        return response()->json([
            'message' => $message,
            'data' => []
        ], 404);
    }
}

// Generate a JSON response with a 400(Bad Request) status code.
if (!function_exists('response400')) {
    function response400($message)
    {
        return response()->json([
            'message' => $message,
            'data' => []
        ], 404);
    }
}

// Generate a JSON response with a 401(Unauthorized) status code.
if (!function_exists('response401')) {
    function response401($message)
    {
        return response()->json([
            'message' => $message,
            'data' => []
        ], 401);
    }
}


// Generate a JSON response with a 201(Creation) status code.
if (!function_exists('response201')) {
    function response201($message, $data = [])
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], 201);
    }
}


// Generate a JSON response with a 500(Server Error) status code.
if (!function_exists('response500')) {
    function response500($message, $error)
    {
        return response()->json([
            'message' => $message,
            'error' => (env('APP_ENV') !== PRODUCTION) ? $error : '',
        ], 500);
    }
}


// Generate a JSON response with a 200(OK) status code.
if (!function_exists('response200')) {
    function response200($message = null, $data = [])
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ]);
    }
}
