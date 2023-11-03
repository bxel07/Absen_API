<?php

namespace App\Http\Controllers\Dashboard\Profile;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FAQController extends Controller
{
    public function index()
    {
        $faqs = FAQ::all();

        if (!$faqs) {
            return response()->json([
                'success'   => true,
                'message'   => 'Data kosong',
            ], 200);
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Detail Post!',
            'data'      => $faqs
        ], 200);
    }

    public function show($id): JsonResponse
    {
        $faq = FAQ::find($id);
        if (!$faq) {
            return response()->json(['message' => 'FAQ not found'], 404);
        }
        return response()->json($faq, 200);
    }

    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'question' => 'required',
            'answer' => 'required',
        ]);

        $faq = new FAQ;
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();

        return response()->json(['message' => 'FAQ created'], 201);
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->validate($request, [
            'question' => 'required',
            'answer' => 'required',
        ]);

        $faq = FAQ::find($id);
        if (!$faq) {
            return response()->json(['message' => 'FAQ not found'], 404);
        }

        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();

        return response()->json(['message' => 'FAQ updated'], 200);
    }

    public function destroy($id): JsonResponse
    {
        $faq = FAQ::find($id);
        if (!$faq) {
            return response()->json(['message' => 'FAQ not found'], 404);
        }

        $faq->delete();

        return response()->json(['message' => 'FAQ deleted'], 200);
    }
}
