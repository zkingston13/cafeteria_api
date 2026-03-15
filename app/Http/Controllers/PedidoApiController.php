<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\DetallePedido;
use Illuminate\Support\Facades\DB;

class PedidoApiController extends Controller
{
    /**
     * Crear un nuevo pedido
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_cliente' => 'nullable|integer|exists:clientes,id_cliente',
            'id_mesa' => 'nullable|integer|exists:mesas,id_mesa',
            'total' => 'required|numeric|min:0',
            'detalles' => 'required|array|min:1',
            'detalles.*.id_producto' => 'required|integer|exists:productos,id_producto',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.subtotal' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Crear el pedido
            $pedido = Pedido::create([
                'id_cliente' => $request->id_cliente,
                'id_mesa' => $request->id_mesa,
                'fecha' => now(),
                'estado' => 'pendiente',
                'total' => $request->total
            ]);

            // Crear los detalles del pedido
            foreach ($request->detalles as $detalle) {
                DetallePedido::create([
                    'id_pedido' => $pedido->id_pedido,
                    'id_producto' => $detalle['id_producto'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'subtotal' => $detalle['subtotal']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pedido creado correctamente',
                'pedido' => $pedido->load('detalles')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el pedido: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todos los pedidos
     */
    public function index()
    {
        $pedidos = Pedido::with(['cliente', 'mesa', 'detalles.producto'])->get();
        
        return response()->json([
            'success' => true,
            'pedidos' => $pedidos
        ]);
    }

    /**
     * Obtener un pedido específico
     */
    public function show($id)
    {
        $pedido = Pedido::with(['cliente', 'mesa', 'detalles.producto'])->find($id);
        
        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'pedido' => $pedido
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
}