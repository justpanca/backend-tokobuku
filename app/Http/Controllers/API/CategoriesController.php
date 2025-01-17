<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {

        $this->middleware(['auth:api', 'admin'])->except(['index', 'show']);
    }


    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'message' => 'Categories berhasil diTampilkan semua.',
            'data' => $categories
        ], 200);
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
        ], [
            'required' => 'name harus diisi!.',
            'min' => 'name minimal 3 karakter.'
        ]);

        $categories = new Category;
        $categories->name = $request->input('name');

        $categories->save();

        return response()->json([

            'message' => 'Categories berhasil dibuat.',
            'data' => $categories,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $categorie = Category::find($id);

        if (!$categorie) {
            return response()->json([

                'message' => "Categori dengan id: $id tidak ditemukan.",

            ], 404);
        }

        return response()->json([

            'message' => "Categori dengan id: $id berhasil ditemukan.",
            'data' => $categorie,
        ], 200);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|min:3',
        ], [
            'required' => 'name harus diisi!.',
            'min' => 'name minimal 3 karakter.'
        ]);

        $categorie = Category::find($id);

        if (!$categorie) {
            return response()->json([

                'message' => "Categori dengan id: $id tidak ditemukan.",

            ], 404);
        }

        $categorie->name = $request->input('name');
        $categorie->save();

        return response([
            'message' => "Berhasil melakukan update Categorie id : $id!",
            'data' => $categorie,
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $categorie = Category::find($id);

        if (!$categorie) {
            return response()->json([

                'message' => "Categori dengan id: $id tidak ditemukan.",

            ], 404);
        }

        $categorie->delete();

        return response([
            'message' => "Berhasil melakukan update Categorie id : $id!",

        ], 200);
    }
}