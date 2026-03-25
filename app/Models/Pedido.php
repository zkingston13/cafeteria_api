<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    protected $primaryKey = 'id_pedido';

    public $timestamps = false;

    protected $fillable = [
        'id_cliente',
        'fecha',
        'estado',
        'total',
        'id_mesa'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'total' => 'decimal:2'
    ];

    protected $attributes = [
        'estado' => 'pendiente'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function mesa()
    {
        return $this->belongsTo(Mesa::class, 'id_mesa', 'id_mesa');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'id_pedido', 'id_pedido');
    }

    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }
    public function scopeDeCliente($query, $id_cliente)
    {
        return $query->where('id_cliente', $id_cliente);
    }

    public function scopeFecha($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    public function getTotalFormateadoAttribute()
    {
        return '$' . number_format($this->total, 2);
    }
    public function getFechaFormateadaAttribute()
    {
        return $this->fecha->format('d/m/Y H:i');
    }

    public function isPendiente()
    {
        return $this->estado === 'pendiente';
    }
    public function isPreparando()
    {
        return $this->estado === 'preparando';
    }
    public function isListo()
    {
        return $this->estado === 'listo';
    }

    public function isEntregado()
    {
        return $this->estado === 'entregado';
    }

    public function isCancelado()
    {
        return $this->estado === 'cancelado';
    }
}