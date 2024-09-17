<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InveUbicacion extends Model
{
    use HasFactory;

    protected $table = 'InveUbicacion';
    protected $primaryKey = 'Id';

    public $timestamps = false;
    protected static $relationsToInclude = [];


    public function trays()
    {
        $relations = ['trays'];
        static::$relationsToInclude = array_merge(static::$relationsToInclude, $relations);
        return $this->hasOne(UbicacionBandeja::class, 'BandejaId', 'BandejaId');
    }

    public function suggestTray()
    {
        $relations = ['suggestTray'];
        static::$relationsToInclude = array_merge(static::$relationsToInclude, $relations);
        return $this->hasOne(UbicacionBandeja::class, 'BandejaId', 'BandejaId');
    }

    public function scopeFilterTrayAndProduct(Builder $query, $product,$tray,$warehouseRq){
        return $query
        ->join('UbicacionBandeja','UbicacionBandeja.BandejaId','=','InveUbicacion.BandejaId')
        ->join('UbicacionMueble',function($join) use($warehouseRq){
            $join->on('UbicacionBandeja.MuebleId','=','UbicacionMueble.Muebleid')
            ->where('UbicacionMueble.Estado','=','A')
            ->where('UbicacionBandeja.Estado','=','A')
            ->where('UbicacionMueble.AlmacenId','=',$warehouseRq);
        })
        ->where('InveUbicacion.BandejaId',$tray)->where('InveUbicacion.ProductoId',$product);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $serializeData = [
            'id'                  => $array['Id'],
            'trayId'              => $array['BandejaId'],
            'productId'           => $array['ProductoId'],
            'lotProductId'        => $array['LoteProductoId'],
            'currentInventory'    => $array['InvenActua'],

        ];
        foreach (static::$relationsToInclude as $relation) {
            if ($this->relationLoaded($relation)) {
                $serializeData[$relation] = $this->$relation;
            }
        }

        return $serializeData;
    }
}
