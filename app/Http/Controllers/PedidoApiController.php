<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\DetallePedido;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PedidoApiController extends Controller
{
    /**
     * Crear un nuevo pedido
     */
public function store(Request $request){
         try {
            DB::beginTransaction();

            // 🔹 Obtener cliente desde token
            $cliente = $request->user();

            // 🔹 Crear pedido (cabecera)
            $pedido = Pedido::create([
                'id_cliente' => $cliente->id_cliente,
                'id_mesa' => $request->id_mesa,
                'total' => $request->total,
                'estado' => 'pendiente'
            ]);

            // 🔹 Guardar detalles
            foreach ($request->detalles as $detalle) {
                DetallePedido::create([
                    'id_pedido' => $pedido->id_pedido,
                    'id_producto' => $detalle['id_producto'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'subtotal' => $detalle['subtotal']
                ]);
            }
  // Debug: Ver qué datos están llegando
        \Log::info('Datos del pedido:', $request->all());
            DB::commit();

            return response()->json([
                'resultado' => true,
                'mensaje' => 'Pedido creado correctamente',
                'pedido' => $pedido,
                'id_pedido' => $pedido->id_pedido
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
             \Log::error('Error en store:', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTraceAsString()
        ]);

            return response()->json([
                'resultado' => false,
                'mensaje' => 'Error al crear pedido',
                'error' => $e->getMessage()
            ], 500);
        }
}

    /**
     * Obtener todos los pedidos
     */
public function index(){
        $pedidos = Pedido::with(['cliente', 'mesa', 'detalles.producto'])->get();
        
        return response()->json([
            'success' => true,
            'pedidos' => $pedidos
        ]);
}

    /**
     * Actualizar estado del pedido
     */
    public function updateEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,preparando,listo,entregado,cancelado'
        ]);

        $pedido = Pedido::find($id);
        
        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado'
            ], 404);
        }

        $pedido->estado = $request->estado;
        $pedido->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'pedido' => $pedido
        ]);
    }

    /**
     * Obtener pedidos por cliente
     */
    public function getByCliente($id_cliente)
    {
        $pedidos = Pedido::with(['detalles.producto'])
                        ->where('id_cliente', $id_cliente)
                        ->orderBy('fecha', 'desc')
                        ->get();

        return response()->json([
            'success' => true,
            'pedidos' => $pedidos
        ]);
    }

    /**
     * Obtener mesas disponibles
     */
    public function getMesas()
    {
        // Aquí puedes obtener las mesas de tu base de datos
        $mesas = \App\Models\Mesa::where('estado', 'disponible')->get();
        
        return response()->json([
            'success' => true,
            'mesas' => $mesas
        ]);
    }
    public function cancelar($id)
{
    $pedido = Pedido::find($id);

    if(!$pedido){
        return response()->json([
            'success'=>false,
            'message'=>'Pedido no encontrado'
        ],404);
    }

    if($pedido->estado == 'cancelado'){
        return response()->json([
            'success'=>false,
            'message'=>'El pedido ya está cancelado'
        ]);
    }

    $pedido->estado = 'cancelado';
    $pedido->save();

    return response()->json([
        'success'=>true,
        'message'=>'Pedido cancelado'
    ]);
}
}