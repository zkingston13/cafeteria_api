<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PedidoApiController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\PagoController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/registro',[ClienteController::class,'store']);

Route::post('/login',[ClienteController::class,'login']);


Route::middleware('auth:api')->group(function () {
    
    Route::post('/logout',[ClienteController::class,'logout']);
    Route::get('/perfil',[ClienteController::class,'perfil']);
    Route::put('/clientes/{id_cliente}',[ClienteController::class,'update']);
    Route::post('/pedidos/store', [PedidoApiController::class, 'store']);
    Route::put('/pedidos/{id}/estado', [PedidoApiController::class, 'updateEstado']);
    Route::get('/pedidos/cliente/{id_cliente}', [PedidoApiController::class, 'getByCliente']);
    Route::get('/mesas', [PedidoApiController::class, 'getMesas']);
    Route::get('/pedido/{id_pedido}/total', [PagoController::class, 'totalPago']);
    Route::post('/pagos', [PagoController::class,'store']);
    });

Route::prefix('productos')->group(function(){
Route::get('/',[ProductosController::class,'index']);
Route::get('/{id_producto}',[ProductosController::class,'show']);
});