<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    public function product(): hasOne
    {
        $relations = ['product'];
        static::$relationsToInclude = array_merge(static::$relationsToInclude, $relations);
        return $this->hasOne(Producto::class, 'productoid', 'ProductoId');
    }


    public function scopeFilterTrayAndProduct(Builder $query, $product, $tray, $warehouseRq)
    {
        return $query
            ->join('UbicacionBandeja', 'UbicacionBandeja.BandejaId', '=', 'InveUbicacion.BandejaId')
            ->join('UbicacionMueble', function ($join) use ($warehouseRq) {
                $join->on('UbicacionBandeja.MuebleId', '=', 'UbicacionMueble.Muebleid')
                    ->where('UbicacionMueble.Estado', '=', 'A')
                    ->where('UbicacionBandeja.Estado', '=', 'A')
                    ->where('UbicacionMueble.AlmacenId', '=', $warehouseRq);
            })
            ->where('InveUbicacion.BandejaId', $tray)->where('InveUbicacion.ProductoId', $product);
    }

    public function scopeWithFilteredProductsAndLocations(Builder $query, $find)
    {
        return $query
            ->join('UbicacionBandeja', 'UbicacionBandeja.BandejaId', '=', 'InveUbicacion.BandejaId')
            ->join('UbicacionMueble', 'UbicacionMueble.MuebleId', '=', 'UbicacionBandeja.Muebleid')
            ->join('vwProducto', 'vwProducto.productoid', '=', 'InveUbicacion.ProductoId')
            ->where('UbicacionBandeja.Estado', 'A')
            ->where(function (Builder $query) use ($find) {
                $query->orWhere('InveUbicacion.ProductoId', $find)
                    ->orWhere('UbicacionBandeja.Barras', $find)
                    ->orWhere('vwProducto.barras', $find)
                    ->orWhere('vwProducto.barras2', $find)
                    ->orWhere('vwProducto.barras3', $find)
                    ->orWhere('vwProducto.referencia', $find);
            });
    }

    public function scopeWithPictureProduct (Builder $query){

        return $query->with(['product'=>function ($q){
            $q->withPictureProduct();
        }]);
    }




    public function scopeWithProduct(Builder $query)
    {
        $relations = ['product'];
        return $query->with($relations);
    }

    public function scopeWithTray(Builder $query)
    {
        $relations = ['trays'];
        return $query->with($relations);
    }

    public function scopeWithWarehouse(Builder $query)
    {
        return $this->load([
            'trays' => function ($query) {
                $query->withPresentation($this->PresentacionId);
            }
        ]);
    }




    public function toArray()
    {
        $array = parent::toArray();
        $serializeData = [
            'id' => $array['Id'],
            'trayId' => $array['BandejaId'],
            'productId' => $array['ProductoId'],
            'lotProductId' => $array['LoteProductoId'],
            'currentInventory' => $array['InvenActua'],

        ];
        foreach (static::$relationsToInclude as $relation) {
            if ($this->relationLoaded($relation)) {
                $serializeData[$relation] = $this->$relation;
            }
        }

        return $serializeData;
    }
}
