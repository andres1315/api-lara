<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
  use HasFactory;

  protected $table = 'vwProducto';


  public $timestamps = false;
  protected static $relationsToInclude = [];

  public function allLocation()
  {
    /* return $this->hasMany(InveUbicacion::class, 'ProductoId', 'productoid')->with(['trays']); */
    return $this->hasMany(InveUbicacion::class, 'ProductoId', 'productoid')
    ->with(['trays']);
  }

  public function suggestedLocation()
  {
    return $this->HasOne(InveUbicacion::class, 'ProductoId', 'productoid')->with(['trays']);
  }

  public static function scopeWithAllLocation(Builder $query)
  {
      $relations = ['allLocation'];
      static::$relationsToInclude = array_merge(static::$relationsToInclude, $relations);
      return $query->with($relations);
  }

  public static function scopeWithSuggestedLocation(Builder $query)
  {
      $relations = ['suggestedLocation'];
      static::$relationsToInclude = array_merge(static::$relationsToInclude, $relations);
      return $query->with(['suggestedLocation']);
  }


  public function toArray($withRelations = false)
  {
    $array = parent::toArray();

    $serializeData = [
      'id'              => @$array['id'],
      'headId'          => @$array['headprodid'],
      'productId'       => @$array['productoid'],
      'reference'       => @$array['referencia'],
      'name'            => @$array['nombre'],
      'type'            => @$array['Tipo'],
      'nameSupplier'    => @$array['nombrprove'],
      'init'            => $array['unidad'],
      'inventoryMonth'  => @$array['inventames'],
      'size'            => ['id' => @$array['tallaid'], 'name' => @$array['nombrtalla']],
      'color'           => ['id' => @$array['colorid'], 'name' => @$array['nombrcolor']],
      'typeProduct'     => ['id' => @$array['tipoproductoid'], 'name' => @$array['nombrtipro']],
      'brand'           => ['id' => @$array['marcaid'], 'name' => @$array['nombrmarca']],
      'state'           => @$array['estado'],
      'cost'            => @$array['costo'],
      'averageCost'     => @$array['costoprome'],
    ];

      foreach (static::$relationsToInclude as $relation) {
          if ($this->relationLoaded($relation)) {
              $serializeData[$relation] = $this->$relation;
          }
      }


    return $serializeData;
  }
}
