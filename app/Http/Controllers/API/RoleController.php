<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'admin']);
    }

    public function index(){
        $Roles = Role::all();
        return response()->json([
            'message' => 'Berhasil menampilkan data role',
            'data' => $Roles
        ], 200);
    }
    public function store (Request $request){
        $request->validate(['name' => 'required|string'],[
       'name.required' => 'Nama role harus disi',
       'name.min' => 'Nama role min 2 karakter'
    ]);
    Role::create([
        'name' => $request->input('name')
    ]);

    return response()->json([
        'message' => 'Berhasil membuat role',
    ], 201);
    }
    public function show($id){
        $Role = Role::with('user')->find($id);
        return response()->json([
            'message' => 'detail data role',
            'data' => $Role
        ], 201);

}
    public function update(Request $request, $id){
     $request->validate(['name' => 'required|string']);
     $Role = Role::find($id);
     $Role->update($request->only('name'));
     return response()->json([
        'message' => 'Berhasil update role',
     ], 200);
}
    public function destroy($id){
        $Role = Role::find($id);
        $Role->delete();
        return response()->jsonnn([
            'message' => 'Berhasil meghapus role'
        ], 200);
    }
 }