<?php

namespace App\Http\Controllers\Dashboard\ProjectManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectManagerController extends Controller
{
    /**
     * Method: Menampilkan pesan bahwa pengguna telah login sebagai Project Manager.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(['Pesan' => 'Anda Login Sebagai Project Manager']);
    }
}
