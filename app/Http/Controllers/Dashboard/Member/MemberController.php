<?php

namespace App\Http\Controllers\Dashboard\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(): JsonResponse
    {
        //Method: Tampilkan halaman utama untuk member.
        return response()->json(['Pesan' => 'Anda Login Sebagai Member']);
    }
}
