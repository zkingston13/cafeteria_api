<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    use HasFactory;

    protected $table = 'detalle_pedidos';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_pedido',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    public $timestamps = false;

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}