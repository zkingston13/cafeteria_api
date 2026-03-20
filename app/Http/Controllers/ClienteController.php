<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class ClienteController extends Controller
{


    public function store(Request $requestuest){

        $cliente = Cliente::create([
            'nombre' => $requestuest->nombre,
            'apellidoP' => $requestuest->apellidoP,
            'apellidoM' => $requestuest->apellidoM,
            'telefono' => $requestuest->telefono,
            'email' => $requestuest->email,
            'fecha_registro' => now(),
            'password' => Hash::make($requestuest->password)
        ]);

        return response()->json([
            'mensaje' => 'Cliente registrado correctamente',
            'cliente' => $cliente
        ],201);

    }
public function update(Request $request, $id_cliente){
    //return response()->json($request->all());
    $validator = Validator::make(
    ['id_cliente' => $id_cliente],
    ['id_cliente' => 'required|integer|min:1|exists:clientes,id_cliente']
    );
    

        if($validator->fails()){
            return response()->json(['resultado'=>false, 'datos' => null,'errors' => $validator->errors()
            ],422);
        }
            try {
    DB::beginTransaction();

    $cliente = Cliente::find($id_cliente);

   
    if($request->filled('nombre'))      $cliente->nombre = $request->nombre;
    if($request->filled('apellidoP'))  $cliente->apellidoP = $request->apellidoP;
    if($request->filled('apellidoM')) $cliente->apellidoM = $request->apellidoM;
    if($request->filled('telefono'))$cliente->telefono = $request->telefono;
    if($request->filled('email'))   $cliente->email = $request->email;
  if($request->filled('password'))   
    $cliente->password = Hash::make($request->password);


    $cliente->save(); 

  
    DB::commit(); 

    return response()->json(['resultado'=>true, 'message' =>'Cliente modificada correctamente'], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Error', 'error' => $e->getMessage()], 500);
    }
}
   public function login(Request $requestuest){

    $cliente = Cliente::where('email',$requestuest->email)->first();

    if(!$cliente || !Hash::check($requestuest->password,$cliente->password)){
        return response()->json([
            'mensaje' => 'Credenciales incorrectas'
        ],401);
    }


    $token = $cliente->createToken('cliente-token')->accessToken;

    return response()->json([
        'mensaje' => 'Login correcto',
        'cliente' => $cliente,
        'token' => $token
    ]);
}

public function logout(Request $requestuest)
{
    $user = $requestuest->user();

    if ($user) {
        $user->token()->revoke();
    }

    return response()->json([
        'mensaje' => 'Sesión cerrada correctamente'
    ]);
}
public function perfil(Request $requestuest)
{
    return response()->json([
        'cliente' => $requestuest->user()
    ]);
}
}


