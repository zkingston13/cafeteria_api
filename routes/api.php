<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PedidoApiController;
use App\Http\Controllers\ProductosController;
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


Route::post('/pedidos', [PedidoApiController::class, 'store']);
Route::get('/pedidos', [PedidoApiController::class, 'index']);
Route::get('/pedidos/{id}', [PedidoApiController::class, 'show']);
Route::put('/pedidos/{id}/estado', [PedidoApiController::class, 'updateEstado']);
Route::get('/pedidos/cliente/{id_cliente}', [PedidoApiController::class, 'getByCliente']);
Route::get('/mesas', [PedidoApiController::class, 'getMesas']);

//productos
Route::prefix('productos')->group(function(){
Route::get('/',[ProductosController::class,'index']);
Route::get('/{id_producto}',[ProductosController::class,'show']);
});