<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacen extends Model{
  use HasFactory;

  protected $table = 'almacen';
  protected $primaryKey = 'almacenid';
  protected $keyType = 'string';
  public $incrementing = false;
  public $timestamps = false;

  public function toArray(){
    $array = parent::toArray();


    $serializeData = [
      'id'                  => $array['almacenid'],
      'name'                => $array['nombre'],
      'isWarehouse'         => $array['bodega'],
      'costCenter'          => $array['centcostid'],
      'state'               => $array['estado'],
    ];

    return $serializeData;
  }

}
