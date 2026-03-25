<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Cliente extends Authenticatable
{
     use HasApiTokens;
     protected $table = 'clientes';
     protected $primaryKey = 'id_cliente';
     public $timestamps = false;

   protected $fillable = [
    'nombre',
    'apellidoP',
    'apellidoM',
    'telefono',
    'email',
    'fecha_registro',
    'password'
];

    protected $hidden = [
        'password'
    ];
}
