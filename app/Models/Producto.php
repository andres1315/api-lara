<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
  use HasFactory;

  protected $table = 'vwProducto';


  public $timestamps = false;


  public function toArray()
  {
    $array = parent::toArray();

    $serializeData = [
      'id'              => $array['id'],
      'headId'          => $array['headprodid'],
      'productid'       => $array['productoid'],
      'reference'       => $array['referencia'],
      'name'            => $array['nombre'],
      'type'            => $array['Tipo'],
      'nameSupplier'    => $array['nombrprove'],
      'init'            => $array['unidad'],
      'inventoryMonth'  => $array['inventames'],
      'size'            => ['id' => $array['tallaid'], 'name' => $array['nombrtalla']],
      'color'           => ['id' => $array['colorid'], 'name' => $array['nombrcolor']],
      'typeProduct'     => ['id' => $array['tipoproductoid'], 'name' => $array['nombrtipro']],
      'brand'           => ['id' => $array['marcaid'], 'name' => $array['nombrmarca']],
      'state'           => $array['estado'],
      'cost'            => $array['costo'],
      'averageCost'     => $array['costoprome'],
    ];

    return $serializeData;
  }
}
