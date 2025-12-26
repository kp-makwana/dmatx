<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
  use AuthorizesRequests;

  public function successResponse($message, $data = [], $status = 200): \Illuminate\Http\JsonResponse
  {
    return response()->json(['success' => true, 'message' => $message, 'data' => $data, 'status' => $status], $status);
  }

  public function errorResponse($message, $data = [], $status = 400): \Illuminate\Http\JsonResponse
  {
    return response()->json(['success' => false, 'message' => $message, 'data' => $data, 'status' => $status], $status);
  }
}
