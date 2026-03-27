<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function store(Request $request)
    {
        $cliente = $request->user();

        $request->validate([
            'id_pedido' => 'required|exists:pedidos,id_pedido',
            'metodo_pago' => 'required|in:efectivo,tarjeta',
            'monto' => 'required|numeric',
            'estado' => 'required|in:pendiente,pagado'
        ]);

        try {
            $pagoId = DB::table('pagos')->insertGetId([
                'id_pedido'   => $request->id_pedido,
                'metodo_pago' => $request->metodo_pago,
                'monto'       => $request->monto,
                'estado'      => $request->estado,
                'fecha_pago'  => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pago registrado correctamente',
                'id_pago' => $pagoId
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    public function totalPago(Request $request, $id_pedido)
    {
        // Obtener el cliente logueado
        $cliente = $request->user();
        
        if (!$cliente) {
            return response()->json([
                "success" => false,
                "message" => "Usuario no autenticado"
            ], 401);
        }
        
        // Buscar el pedido que pertenece al cliente logueado
        $pedido = Pedido::where('id_pedido', $id_pedido)
                        ->where('id_cliente', $cliente->id_cliente)
                        ->first();
        
        if (!$pedido) {
            return response()->json([
                "success" => false,
                "message" => "Pedido no encontrado"
            ], 404);
        }
        
        return response()->json([
            "success" => true,
            "total" => $pedido->total,
            "id_pedido" => $pedido->id_pedido,
            "estado" => $pedido->estado
        ]);
    }

}