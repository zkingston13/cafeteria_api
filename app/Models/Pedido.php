<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'pedidos';

    /**
     * La llave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_pedido';

    /**
     * Indica si el modelo tiene timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_cliente',
        'fecha',
        'estado',
        'total',
        'id_mesa'
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha' => 'datetime',
        'total' => 'decimal:2'
    ];

    /**
     * Los valores por defecto para los atributos.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'estado' => 'pendiente'
    ];

    /**
     * Obtiene el cliente asociado al pedido.
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    /**
     * Obtiene la mesa asociada al pedido.
     */
    public function mesa()
    {
        return $this->belongsTo(Mesa::class, 'id_mesa', 'id_mesa');
    }

    /**
     * Obtiene los detalles del pedido.
     */
    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'id_pedido', 'id_pedido');
    }

    /**
     * Scope para filtrar por estado.
     */
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para filtrar por cliente.
     */
    public function scopeDeCliente($query, $id_cliente)
    {
        return $query->where('id_cliente', $id_cliente);
    }

    /**
     * Scope para filtrar por fecha.
     */
    public function scopeFecha($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    /**
     * Obtiene el total formateado.
     */
    public function getTotalFormateadoAttribute()
    {
        return '$' . number_format($this->total, 2);
    }

    /**
     * Obtiene la fecha formateada.
     */
    public function getFechaFormateadaAttribute()
    {
        return $this->fecha->format('d/m/Y H:i');
    }

    /**
     * Verifica si el pedido está pendiente.
     */
    public function isPendiente()
    {
        return $this->estado === 'pendiente';
    }

    /**
     * Verifica si el pedido está en preparación.
     */
    public function isPreparando()
    {
        return $this->estado === 'preparando';
    }

    /**
     * Verifica si el pedido está listo.
     */
    public function isListo()
    {
        return $this->estado === 'listo';
    }

    /**
     * Verifica si el pedido está entregado.
     */
    public function isEntregado()
    {
        return $this->estado === 'entregado';
    }

    /**
     * Verifica si el pedido está cancelado.
     */
    public function isCancelado()
    {
        return $this->estado === 'cancelado';
    }
}