<?php

namespace App\Http\Controllers\Dashboard\Account;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FAQController extends Controller
{
    //Mendapatkan daftar semua FAQ
    public function index(): JsonResponse
    {
        // Dapatkan semua data FAQ
        $faqs = Faq::all();
        // Jika tidak ada data FAQ yang ditemukan, kembalikan respon JSON dengan pesan "Data kosong"
        if (!$faqs) {
            return response()->json([
                'success'   => true,
                'message'   => 'Data kosong',
            ], 200);
        }
        // Kembalikan respon JSON dengan data FAQ yang ditemukan
        return response()->json([
            'success'   => true,
            'message'   => 'Detail Post!',
            'data'      => $faqs
        ], 200);
    }

    //Mendapatkan detail FAQ berdasarkan ID
    public function show($id): JsonResponse
    {
        // Cari FAQ berdasarkan ID
        $faq = Faq::find($id);
        // Jika FAQ tidak ditemukan, kembalikan respon JSON dengan pesan "FAQ not found"
        if (!$faq) {
            return response()->json(['message' => 'FAQ not found'], 404);
        }
        // Kembalikan respon JSON dengan detail FAQ
        return response()->json($faq, 200);
    }

    //Membuat FAQ baru
    public function store(Request $request): JsonResponse
    {
        // Lakukan validasi terhadap data yang dikirim
        $this->validate($request, [
            'question' => 'required',
            'answer' => 'required',
        ]);
        // Buat FAQ baru
        $faq = new Faq;
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();
        // Kembalikan respon JSON dengan pesan "FAQ created"
        return response()->json(['message' => 'FAQ created'], 201);
    }

    /**
     * @throws ValidationException
     */
    //Memperbarui FAQ berdasarkan ID
    public function update(Request $request, $id): JsonResponse
    {
        // Lakukan validasi terhadap data yang dikirim
        $this->validate($request, [
            'question' => 'required',
            'answer' => 'required',
        ]);
        // Cari FAQ berdasarkan ID
        $faq = Faq::find($id);
        // Jika FAQ tidak ditemukan, kembalikan respon JSON dengan pesan "FAQ not found"
        if (!$faq) {
            return response()->json(['message' => 'FAQ not found'], 404);
        }
        // Perbarui FAQ
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();
        // Kembalikan respon JSON dengan pesan "FAQ updated"
        return response()->json(['message' => 'FAQ updated'], 200);
    }

    //Menghapus FAQ berdasarkan ID
    public function destroy($id): JsonResponse
    {
        // Cari FAQ berdasarkan ID
        $faq = Faq::find($id);
        // Jika FAQ tidak ditemukan, kembalikan respon JSON dengan pesan "FAQ not found"
        if (!$faq) {
            return response()->json(['message' => 'FAQ not found'], 404);
        }
        // Hapus FAQ
        $faq->delete();
        // Kembalikan respon JSON dengan pesan "FAQ deleted"
        return response()->json(['message' => 'FAQ deleted'], 200);
    }
}
