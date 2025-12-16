<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }
}