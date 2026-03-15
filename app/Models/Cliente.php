<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
     protected $table = 'clientes';
     protected $primaryKey = 'id_cliente';
     public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellidoP',
        'apellidoM',
        'email',
        'telefono',
        'fecha_registro',
        'password'
    ];

    protected $hidden = [
        'password'
    ];
}
