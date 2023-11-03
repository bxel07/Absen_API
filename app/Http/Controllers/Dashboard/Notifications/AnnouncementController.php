<?php

namespace App\Http\Controllers\Dashboard\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{

    public function index()
    {
        $allAnnouncements = Announcement::latest()->get();
        return response()->json([
            'success' => true,
            'message' => 'List Data Pengumuman',
            'data'    => $allAnnouncements
        ], 200);
    }

    public function store(Request $request)
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
                    'data' =>[ 'picture' => $url, 'message' => $request->message]
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

    public function update(Request $request, $announcementId)
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

    public function destroy($announcementId)
    {
        $announcement = Announcement::where('id', $announcementId)->first();
        if (!is_null($announcement->picture)) {
            $data = basename($announcement->picture);
            Storage::delete('public/images/'.$data);
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
