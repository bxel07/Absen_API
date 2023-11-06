<?php

namespace App\Http\Controllers\Dashboard\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class AnnouncementController extends Controller
{

    /**
     * Displays entire list of announcements.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/list-announcements",
     *     summary="Displays entire list of announcements",
     *     description="All users can see announcements made by the project manager.",
     *     tags={"Announcement"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success: Announcement data list was successfully retrieved.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string", example="Announcement Data List"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="picture", type="string", example="xTZCMwEpjGda3cIy4up6nMhu5urO5vufMaKjsBzP.jpg"),
     *                     @OA\Property(property="message", type="string", example="Announcement Title"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-26T06:20:35.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-26T06:20:35.000000Z"),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error: Announcement Data List Not Found!",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Announcement Data List Not Found"),
     *             @OA\Property(property="data", type="string", example=null),
     *         ),
     *     ),
     * )
     */
    public function index(): JsonResponse
    {
        $allAnnouncements = Announcement::latest()->get();
        if ($allAnnouncements) {
            return response()->json([
                'success' => true,
                'message' => 'Announcement Data List',
                'data'    => $allAnnouncements
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Announcement Data List Not Found!',
                'data'    => null
            ], 404);
        }
    }

    /**
     * Method: Menyimpan pengumuman baru.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'picture' => 'required|image|mimes:png,jpg,jpeg',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data input harus dilengkapi!',
                'data' => $validator->errors(),
            ], 422);
        } else {
            $picture = $request->file('picture');
            $picture->storeAs('public/images', $picture->hashName());

            $getAllRequest = $request->all();
            $getAllRequest['picture'] = $picture->hashName();
            $url = Storage::url('public/images/' . $getAllRequest['picture']);

            //            $createAnnouncement = Announcement::create($getAllRequest);
            $createAnnouncement = Announcement::create([
                'picture' => $url,
                'message' => $request->message
            ]);
            if ($createAnnouncement) {
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil membuat pengumuman baru!',
                    'data' => ['picture' => $url, 'message' => $request->message]
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat pengumuman baru!',
                    'data' => null,
                ], 404);
            }
        }
    }

    /**
     * Method: Memperbarui pengumuman berdasarkan ID.
     *
     * @param Request $request
     * @param int $announcementId
     * @return JsonResponse
     */
    public function update(Request $request, $announcementId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'picture' => 'image|mimes:png,jpg,jpeg',
            'message' => 'required',
        ]);

        $data = Announcement::findOrFail($announcementId);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data input harus dilengkapi!',
                'data' => $validator->errors(),
            ], 422);
        } else {
            if ($request->hasFile('picture')) {
                Storage::delete('public/images/' . $data->picture);
                $picture = $request->file('picture');
                $picture->storeAs('public/images', $picture->hashName());
                $getAllRequest = $request->all();
                $getAllRequest['picture'] = $picture->hashName();
                $data->update($getAllRequest);

                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil memperbarui pengumuman!',
                    'data' => $request->all(),
                ], 201);
            } else {
                $data->message = $request->message;
                $data->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil memperbarui pengumuman!',
                    'data' => $request->all(),
                ], 201);
            }
        }
    }

    /**
     * Method: Menghapus pengumuman berdasarkan ID.
     *
     * @param int $announcementId
     * @return JsonResponse
     */
    public function destroy($announcementId): JsonResponse
    {
        $announcement = Announcement::where('id', $announcementId)->first();
        if (!is_null($announcement->picture)) {
            $data = basename($announcement->picture);
            Storage::delete('public/images/' . $data);
        }
        $delAnnouncement = Announcement::find($announcementId)->delete();
        if ($delAnnouncement) {
            return response()->json([
                'success' => true,
                'message' => 'Data ID pengumuman ' . $announcementId . ' berhasil dihapus!',
                'data' => null,
            ], 200);
        }
    }
}
