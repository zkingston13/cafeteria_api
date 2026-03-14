<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Categoria;

class Productos extends Model{
 protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
          'nombre',
    'descripcion',
    'precio',
    'id_categoria',
    'disponible'
    ];

public function categorias()
{
    return $this->belongsTo(Categoria::class, 'id_categoria');
}
}