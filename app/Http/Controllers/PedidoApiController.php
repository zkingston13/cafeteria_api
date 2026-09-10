<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Productos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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

    $producto = Productos::find($detalle['id_producto']);

 if (!$producto->disponible) {
    throw new Exception("El producto {$producto->nombre} no está disponible");
}

   

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
                'pedido' => $pedido->load(['cliente','mesa','detalles'])
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el pedido: ' . $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, $id)
{   
    $request->merge(json_decode($request->getContent(), true) ?? []);
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

    $pedido = Pedido::find($id);

    if (!$pedido) {
        return response()->json([
            'success' => false,
            'message' => 'Pedido no encontrado'
        ], 404);
    }

    try {
        DB::beginTransaction();

        // 1. Actualizar el pedido principal
        $pedido->update([
            'id_cliente' => $request->id_cliente,
            'id_mesa'    => $request->id_mesa,
            'total'      => $request->total
        ]);

        // 2. Reemplazar detalles
        $pedido->detalles()->delete();

        foreach ($request->detalles as $detalle) {
            $producto = Productos::find($detalle['id_producto']);
            if (!$producto || !$producto->disponible) {
                throw new \Exception("El producto con ID {$detalle['id_producto']} no está disponible");
            }

            DetallePedido::create([
                'id_pedido'       => $pedido->id_pedido,
                'id_producto'     => $detalle['id_producto'],
                'cantidad'        => $detalle['cantidad'],
                'precio_unitario' => $detalle['precio_unitario'],
                'subtotal'        => $detalle['subtotal']
            ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Pedido actualizado correctamente',
            'pedido'  => $pedido->fresh()->load(['cliente', 'mesa', 'detalles.producto'])
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar el pedido: ' . $e->getMessage()
        ], 500);
    }
}
   
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

    public function destroy($id_pedido)
{

      $validator = Validator::make(
 ['id_pedido' => $id_pedido],
 ['id_pedido' => 'required|integer|min:1|exists:pedidos,id_pedido']
);

if($validator->fails()){
    return response()->json(['resultado'=>false, 'datos' => null,'errors' => $validator->errors()
    ],422);
}

   


   try {
    DB::beginTransaction();
     $pedido = Pedido::find($id_pedido);
     $pedido->detalles()->delete();
     $pedido->delete();
     DB::commit();
     return response()->json([
        'resultado' => true,
        'datos' => $pedido
     ],200);

   } catch (\Exception $e) {
    DB::rollback();
    return response()->json([
        'resultado' => false,
        'message' => 'Error al eliminar el pedido' . $e->getMessage()
    ], 500);
   }

    

   

    return response()->json(['resultado'=>true, 'datos'=>$pedido],200);
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