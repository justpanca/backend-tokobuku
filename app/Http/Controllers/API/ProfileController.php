<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;

class ProfileController extends Controller
{
    public function storeupdate(Request $request )
    {
        $user = auth()->user();

        $request->validate([
            'bio' => 'required',
            'age' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'required' => 'Inputan :attribute harus diisi',
            'integer' => 'Inputan :attribute harus bernilai angka',
        ]);

        // $profile = Profile::find($id);
        $profile = Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'bio' => $request->input('bio'),
                'age' => $request->input('age'),
                'user_id' => $user->id,
            ]);

        if (!$profile) {
            return response([
                "message" => "Data profile tidak ditemukan",
            ], 404);
        }

        if ($request->hasFile('image')) {
            $uploadedFileUrl = cloudinary()->upload($request->file('image')->getRealPath(), [
                'folder' => 'image',
            ])->getSecurePath();
            $profile->image = $uploadedFileUrl;
        }

        $profile->save();

        return response([
            "message" => "Profile berhasil dibuat/diupdate",
            "data" => $profile,
        ], 201);
    }
}
