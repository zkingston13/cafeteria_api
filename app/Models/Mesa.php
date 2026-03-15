<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    use HasFactory;

    protected $table = 'mesas';
    protected $primaryKey = 'id_mesa';

    protected $fillable = [
        'numero',
        'estado'
    ];

    public $timestamps = false;

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_mesa');
    }
}