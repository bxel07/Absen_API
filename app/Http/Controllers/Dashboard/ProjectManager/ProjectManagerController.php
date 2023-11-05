<?php

namespace App\Http\Controllers\Dashboard\ProjectManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectManagerController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['Pesan' => 'Anda Login Sebagai Project Manager']);
    }
}
