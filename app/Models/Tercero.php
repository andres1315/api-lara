<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tercero extends Model
{
    use HasFactory;

    protected $table = 'tercero';
    protected $primaryKey = 'TerceroID';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    //Accesor: Asegurar de limpiar espacios en la columna terceroid antes de utilizar la tabla en consultas o relaciones
    protected function TerceroID(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => trim($value),
        );
    }

    public function toArray()
    {
      $array = parent::toArray();


      $serializeData = [
        'thirdId'           => $array['TerceroID'],
        'name'              => $array['nombre'],
        'typeDocument'      => $array['tipodocuid'],
        'cityId'            => $array['ciudadid'],
        'cellphone'         => $array['celular'],
        'phone'             => $array['telefono'],
        'photo'             => $array['foto'],
        'state'             => $array['Estado'],
      ];

      return $serializeData;
    }

}
