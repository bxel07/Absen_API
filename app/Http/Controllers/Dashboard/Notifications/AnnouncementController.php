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
     * Save new announcements.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/api/add-announcement",
     *     summary="Save new announcements.",
     *     description="Project manager makes a new announcement for users.",
     *     tags={"Announcement"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="picture", type="string", format="binary", description="Supporting images for announcements."),
     *             @OA\Property(property="message", type="string", description="Announcement message."),
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
     *                     property="picture",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The picture field is required."},
     *                     description="Pesan kesalahan untuk bidang 'picture'."
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The message field is required."},
     *                     description="Pesan kesalahan untuk bidang 'message'."
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success: Successfully added a new announcement.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="boolean", example="Successfully added a new announcement."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="picture", type="string", example="/storage/images/a9qy0q3d1xCkrVy55m34vxuIx9dbXcvmLmQLDwnO.jpg", description="URL gambar."),
     *                 @OA\Property(property="message", type="string", example="Announcement message.",
     *                     description="Pesan atau informasi terkait gambar."),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error: Failed to make a new announcement!",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to make a new announcement!"),
     *             @OA\Property(property="data", type="string", example=null),
     *         ),
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
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
                'message' => 'Validation failed. Please check your input data!',
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
                    'message' => 'Successfully added a new announcement.',
                    'data' => ['picture' => $url, 'message' => $request->message]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to make a new announcement!',
                    'data' => null,
                ], 400);
            }
        }
    }

    /**
     * Updates announcements by ID.
     *
     * @param Request $request
     * @param int $announcementId
     * @return JsonResponse
     *
     * @OA\Put(
     *     path="/api/update-announcement/{announcementId}",
     *     summary="Updates announcements by ID.",
     *     description="Project managers can change announcements based on selected data.",
     *     tags={"Announcement"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="picture", type="string", format="binary", description="Supporting images for announcements."),
     *             @OA\Property(property="message", type="string", description="Announcement message."),
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
     *                     property="picture",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The picture field is required."},
     *                     description="Pesan kesalahan untuk bidang 'picture'."
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The message field is required."},
     *                     description="Pesan kesalahan untuk bidang 'message'."
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success: Successfully updated the announcement.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="boolean", example="Successfully updated the announcement."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="picture", type="string", example="/storage/images/a9qy0q3d1xCkrVy55m34vxuIx9dbXcvmLmQLDwnO.jpg", description="URL gambar."),
     *                 @OA\Property(property="message", type="string", example="Announcement message.",
     *                     description="Pesan atau informasi terkait gambar."),
     *             ),
     *         ),
     *     ),
     *     security={{ "bearerAuth": {} }}
     * )
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
                'message' => 'Validation failed. Please check your input data!',
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
                    'message' => 'Successfully updated the announcement.',
                    'data' => $request->all(),
                ], 201);
            } else {
                $data->message = $request->message;
                $data->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Successfully updated the announcement.',
                    'data' => $request->all(),
                ], 201);
            }
        }
    }

    /**
     * Remove the specified announcement.
     *
     * @param int $announcementId
     * @return JsonResponse
     *
     * @OA\Delete(
     *     path="/api/delete-announcement/{announcementId}",
     *     summary="Remove the specified announcement.",
     *     tags={"Announcement"},
     *     @OA\Parameter(
     *         name="announcementId",
     *         in="path",
     *         required=true,
     *         description="Project managers can delete specified announcements.",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Announcement successfully deleted.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Announcement ID data ($announcementId) deleted successfully!"),
     *             @OA\Property(property="data", type="null", example=null),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ID Announcement ($announcementId) not found!",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="ID Announcement ($announcementId) not found!"),
     *             @OA\Property(property="data", type="null", example=null),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to delete announcement!",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to delete announcement!"),
     *             @OA\Property(property="data", type="null", example=null),
     *         )
     *     )
     * )
     */
    public function destroy($announcementId): JsonResponse
    {
        $announcement = Announcement::find($announcementId);

        if ($announcement) {
            if (!is_null($announcement->picture)) {
                $data = basename($announcement->picture);
                Storage::delete('public/images/' . $data);
            }
            if ($announcement->delete()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Announcement ID data (' . $announcementId . ') deleted successfully!',
                    'data' => null,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete announcement!',
                    'data' => null,
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'ID Announcement ' . $announcementId . ' not found!',
                'data' => null,
            ], 404);
        }
    }
}
