<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Segur extends Model
{
    use HasFactory;

    protected $table = 'segur';
    protected $primaryKey = 'usuarioId';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    public function toArray()
  {
    $array = parent::toArray();
    $serializeData = [
      'id'              => $array['usuarioId'],
      'name'            => $array['nombre'],
      'state'           => $array['estado'],
      'document'        => $array['cedula'],
      'warehouseId'     => $array['AlmacenId'],

    ];

    return $serializeData;
  }

}
