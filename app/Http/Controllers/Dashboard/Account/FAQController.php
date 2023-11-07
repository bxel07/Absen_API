<?php

namespace App\Http\Controllers\Dashboard\Account;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FAQController extends Controller
{
    /**
     * Displays entire list of FAQ.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/faq",
     *     summary="Displays entire list of FAQ",
     *     description="All users can see FAQ made by the project manager.",
     *     tags={"FAQ"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success: FAQ data list was successfully retrieved.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string", example="FAQ Data List"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="question", type="string", example="Buat Tugas Baru"),
     *                     @OA\Property(property="answer", type="text", example=" Lorem ipsum dolor sit amets"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-26T06:20:35.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-26T06:20:35.000000Z"),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: FAQ Data List Not Found!",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="FAQ Data List Not Found"),
     *             @OA\Property(property="data", type="string", example=null),
     *         ),
     *     ),
     * )
     */
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



    /**
     * Display notification history based on user ID.
     *
     * @param int $id
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/faq/{id}",
     *     summary="Display FAQ history based on user ID",
     *     description="All users can see FAQ made by ID the project manager.",
     *     tags={"FAQ"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success: User notification data history was successfully retrieved.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string", example="List of Notification Data from User ID (2)"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="question", type="string", example="Tentang Perusahaan"),
     *                     @OA\Property(property="answer", type="text", example=" Lorem ipsum dolor sit amets"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-9-26T06:20:35.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-9-26T06:20:35.000000Z"),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: User FAQ Data History Not Found!",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User FAQ Data History Not Found"),
     *             @OA\Property(property="data", type="string", example=null),
     *         ),
     *     ),
     * )
     */
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


    /**
     * Save new FAQ.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/api/faq",
     *     summary="Save new FAQ.",
     *     description="Project manager makes a new FAQ for users.",
     *     tags={"FAQ"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", description="Unique ID to identify specific data."),
     *             @OA\Property(property="question", type="string", description="Create a new FAQ question."),
     *             @OA\Property(property="answer", type="text", description="FAQ answer."),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error: Validation failed. Please check your input data!",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed. Please check your input data!"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="array",
     *                     @OA\Items(type="integer"),
     *                     example={"The id field is required."},
     *                     description="Pesan kesalahan untuk bidang 'id'."
     *                 ),
     *                 @OA\Property(
     *                     property="question",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The question field is required."},
     *                     description="Pesan kesalahan untuk bidang 'question'."
     *                 ),
     *                 @OA\Property(
     *                     property="answer",
     *                     type="array",
     *                     @OA\Items(type="text"),
     *                     example={"The answer field is required."},
     *                     description="Pesan kesalahan untuk bidang 'answer'."
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success: Successfully added a new FAQ.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="boolean", example="Successfully added a new FAQ."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example="3", description="Unique ID to identify specific data."),
     *                 @OA\Property(property="question", type="string", example="Klaim Poin", description="New question."),
     *                 @OA\Property(property="answer", type="text", example="Lorem ipsum dolor sit amets.",
     *                     description="New answer."),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error: Failed to make a new FAQ!",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to make a new FAQ!"),
     *             @OA\Property(property="data", type="string", example=null),
     *         ),
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
     */
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
     * Updates FAQ by ID.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     *
     * @OA\Put(
     *     path="/api/faq/{id}",
     *     summary="Updates FAQ by ID.",
     *     description="Project managers can change FAQ based on selected data.",
     *     tags={"FAQ"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", description="Unique ID to identify specific data."),
     *             @OA\Property(property="question", type="string", description="Create a new FAQ question."),
     *             @OA\Property(property="answer", type="text", description="FAQ answer."),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error: Validation failed. Please check your input data!",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed. Please check your input data!"),
     *             @OA\Property(property="data", type="object",
     *                  @OA\Property(
     *                     property="id",
     *                     type="array",
     *                     @OA\Items(type="integer"),
     *                     example={"The id field is required."},
     *                     description="Pesan kesalahan untuk bidang 'id'."
     *                 ),
     *                 @OA\Property(
     *                     property="question",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The question field is required."},
     *                     description="Pesan kesalahan untuk bidang 'question'."
     *                 ),
     *                 @OA\Property(
     *                     property="answer",
     *                     type="array",
     *                     @OA\Items(type="text"),
     *                     example={"The answer field is required."},
     *                     description="Pesan kesalahan untuk bidang 'answer'."
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success: Successfully updated the FAQ.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="boolean", example="Successfully updated the announcement."),
     *             @OA\Property(property="data", type="object",
     *                @OA\Property(property="id", type="integer", example="3", description="Unique ID to identify specific data."),
     *                 @OA\Property(property="question", type="string", example="Tambahkan Anggota Proyek", description="New question."),
     *                 @OA\Property(property="answer", type="text", example="Lorem ipsum dolor sit amets.",
     *                     description="New answer."),
     *             ),
     *         ),
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
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


    /**
     * Remove the specified announcement.
     *
     * @param int $id
     * @return JsonResponse
     *
     * @OA\Delete(
     *     path="/api/faq/{id}",
     *     summary="Remove the specified FAQ.",
     *     tags={"FAQ"},
     *     @OA\Parameter(
     *         name="announcementId",
     *         in="path",
     *         required=true,
     *         description="Project managers can delete specified FAQ.",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="FAQ successfully deleted.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Announcement ID data ($id) deleted successfully!"),
     *             @OA\Property(property="data", type="null", example=null),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ID FAQ ($id) not found!",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="ID FAQ ($id) not found!"),
     *             @OA\Property(property="data", type="null", example=null),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to delete FAQ!",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to delete FAQ!"),
     *             @OA\Property(property="data", type="null", example=null),
     *         )
     *     )
     * )
     */
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
