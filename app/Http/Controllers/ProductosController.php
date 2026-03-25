<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;

use App\Models\Categoria;
use App\Models\Productos;

Class ProductosController extends Controller{
    
public function index() {

$productos= Productos::with('categorias')->get();
 return response()->json(['resultado'=>true, 'datos' =>$productos], 200);
}

public function show($id_producto){
    $validator = Validator::make(
        ['id_producto'=> $id_producto],
        ['id_producto' => 'required|integer|min:1|exists:productos,id_producto']
    );
    if($validator->fails()){
        return response()->json(['resultado'=>false, 'datos' => null,'errors'=>$validator->errors()
        ],422);
    }

    $producto= Productos::with('categorias')->find($id_producto);
    if(!$producto){
        return response()->json([
            'resultado' => false,
            'datos' => null
        ], 404);
    }
    return response()->json(['resultado'=>true, 'datos'=>$producto],200);


}
}