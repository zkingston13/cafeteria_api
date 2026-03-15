<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClienteController extends Authenticatable
{


    public function store(Request $request){

        $request->validate([
            'nombre' => 'required|max:100',
            'telefono' => 'nullable|max:15',
            'email' => 'required|email|unique:clientes,email',
            'password' => 'required|min:6'
        ]);

        $cliente = Cliente::create([
            'nombre' => $request->nombre,
            'apellidoP' => $request->apellidoP,
            'apellidoM' => $request->apellidoM,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'fecha_registro' => now(),
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'mensaje' => 'Cliente registrado correctamente',
            'cliente' => $cliente
        ],201);

    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $cliente = Cliente::where('email',$request->email)->first();

        if(!$cliente || !Hash::check($request->password,$cliente->password)){
            return response()->json([
                'mensaje' => 'Credenciales incorrectas'
            ],401);
        }

        return response()->json([
            'mensaje' => 'Login correcto',
            'cliente' => $cliente
        ]);
    }


}