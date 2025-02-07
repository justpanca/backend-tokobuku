<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\Product;

class ProductController extends Controller
{
    public function __construct()
    {

        $this->middleware(['auth:api', 'admin'])->except(['index', 'show']);
    }
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search')) {
            $searching = $request->input('search');
            $query->where('name', "LIKE", "%$searching%");
        }

        $per_page = $request->input('per_page', 6);

        $products = $query->paginate($per_page);

        return response()->json([
            'message' => 'Product berhasil diTampilkan semua.',
            'data' => $products
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // Validasi input
    $request->validate([
        'name' => 'required|min:3',
        'image' => 'required|mimes:jpeg,png,jpg|max:2048',
        'description' => 'required',
        'price' => 'required|integer',
        'category_id' => 'required|exists:categories,id',
        'stock' => 'required|integer'
    ], [
        'required' => 'Input :attribute harus diisi!',
        'max' => 'Input :attribute maksimal :max bytes',
        'mimes' => 'Input :attribute harus berformat jpeg, png, jpg',
        'image' => 'Input :attribute harus berupa gambar',
        'exists' => 'Input :attribute tidak ditemukan di tabel categories!',
        'integer' => 'Input :attribute harus berupa angka.',
    ]);

    // Cek apakah file ada sebelum mengupload ke Cloudinary
    if (!$request->hasFile('image')) {
        return response()->json([
            'message' => 'Gambar harus diunggah!',
        ], 400);
    }

    try {
        // Upload gambar ke Cloudinary
        $uploadedFileUrl = cloudinary()->upload($request->file('image')->getRealPath(), [
            'folder' => 'images',
        ])->getSecurePath();

        // Simpan data produk
        $product = new Product();
        $product->name = $request->input('name');
        $product->price = $request->input('price');
        $product->description = $request->input('description');
        $product->image = $uploadedFileUrl;
        $product->stock = $request->input('stock');
        $product->category_id = $request->input('category_id');

        if ($product->save()) {
            return response()->json([
                'message' => "Product berhasil dibuat",
                'data' => $product,
            ], 201);
        } else {
            return response()->json([
                'message' => 'Gagal menyimpan produk!',
            ], 500);
        }
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
        ], 500);
    }
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['category'])->find($id);

        if (!$product) {
            return response()->json([
                'message' => "Product tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'message' => "Product berhasil ditemukan.",
            'data' => $product,
        ], 200);
    }

    public function update(Request $request, string $id)
    {

        $request->validate([
            'image' => 'mimes:jpeg,png,jpg|max:2048',
            'name' => 'required|min:3',
            'description' => 'required',
            'price' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer'
        ], [
            'required' => 'input :attribute harus diisi!.',
            'max' => 'input :atribut minimal :max bite',
            'mimes' => 'input :atribut harus berformat jpeg,png,jpg',
            'image' => 'input :atribut harus gambar',
            'exists' => 'input :attribute tidak ditemukan di table genres!',
            'integer' => 'input :attribute harus berupa angka.',

        ]);

        $product = Product::find($id);


        if ($request->hasFile('image')) {
            $uploadedFileUrl = cloudinary()->upload($request->file('image')->getRealPath(), [
                'folder' => 'images',
            ])->getSecurePath();
            $product->image =  $uploadedFileUrl;
        }


        if (!$product) {
            return response()->json([
                'message' => "Product tidak ditemukan",
            ], 404);
        }


        $product->name = $request->input('name');
        $product->price = $request->input('price');
        $product->description = $request->input('description');
        $product->stock = $request->input('stock');
        $product->category_id = $request->input('category_id');

        $product->save();


        return response()->json([
            'message' => "Update Product berhasil!",
            'data' => $product
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)

    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => "Product  tidak ditemukan",
            ], 404);
        }
        $product->delete();

        return response()->json([
            'message' => "Berhasil Menghapus product"
        ], 200);
    }
}