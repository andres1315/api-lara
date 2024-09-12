<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoviUbicacion extends Model
{
    use HasFactory;

    protected $table = 'MoviUbicacion';
    protected $primaryKey = 'MovimientoId';
    public $timestamps = false;
    protected $fillable = ['Fecha', 'TipoMovimiento', 'BandejaId','ProductoId','Cantidad','TipoOrigen','NumeroOrigen','FechaRegistro','UsuarioId','AlmacenId'];

}
