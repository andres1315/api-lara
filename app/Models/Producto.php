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
        return $this->hasMany(InveUbicacion::class, 'ProductoId', 'productoid')
            ->with(['trays']);
    }

    public function scopeWithLocationOnWarehouse(Builder $query, $wareHouseId = null)
    {
        $relations = ['allLocation'];
        static::$relationsToInclude = array_merge(static::$relationsToInclude, $relations);
        $locationOnWarehouse = $query->with([
            'allLocation' => function ($q) use ($wareHouseId) {
                $q
                    ->join('UbicacionBandeja', 'UbicacionBandeja.BandejaId', '=', 'InveUbicacion.BandejaId')
                    ->join('UbicacionMueble', function ($join) use ($wareHouseId) {
                        $join->on('UbicacionBandeja.MuebleId', '=', 'UbicacionMueble.Muebleid')
                            ->where('UbicacionMueble.Estado', '=', 'A')
                            ->where('UbicacionBandeja.Estado', '=', 'A')
                            ->where('UbicacionMueble.AlmacenId', '=', $wareHouseId);
                    });
            }
        ]);
        return $locationOnWarehouse;
    }

    public function suggestedLocation()
    {
        return $this->HasOne(InveUbicacion::class, 'ProductoId', 'productoid')->with(['trays']);
    }

    public function scopeWithAllLocation(Builder $query)
    {
        $relations = ['allLocation'];
        static::$relationsToInclude = array_merge(static::$relationsToInclude, $relations);
        return $query->with($relations);
    }

    public function scopeWithMainSuggestedLocation(Builder $query, $warehouseRq)
    {
        $relations = ['suggestedLocation'];
        static::$relationsToInclude = array_merge(static::$relationsToInclude, $relations);
        $suggested = $query->with([
            'suggestedLocation' => function ($q) use ($warehouseRq) {
                $q
                    ->join('UbicacionBandeja', 'UbicacionBandeja.BandejaId', '=', 'InveUbicacion.BandejaId')
                    ->join('UbicacionMueble', function ($join) use ($warehouseRq) {
                        $join->on('UbicacionBandeja.MuebleId', '=', 'UbicacionMueble.Muebleid')
                            ->where('UbicacionMueble.Estado', '=', 'A')
                            ->where('UbicacionBandeja.Estado', '=', 'A')
                            ->where('UbicacionMueble.AlmacenId', '=', $warehouseRq);
                    });
            }
        ]);
        return $suggested;
    }

    public function scopeWithPresentation(Builder $query, $presentationId = null)
    {

        return $query->when($presentationId, function ($query) use ($presentationId) {
            $query->join('Presentacion', function ($join) use ($presentationId) {
                $join->on('vwProducto.headprodid', '=', 'Presentacion.HeadProdId')
                    ->where('vwProducto.ManejPrese', '=', 'S')
                    ->where('Presentacion.PresentacionId', '=', $presentationId)
                    ->where('Presentacion.Estado', '=', 'A');
            })
                ->select('vwProducto.*', 'Presentacion.PresentacionId', 'Presentacion.Nombre as namePresentation');
        });
    }

    public function productPresentation()
    {
        return $this->hasMany(Presentacion::class, 'HeadProdId', 'headprodid');
    }


    public function toArray()
    {
        $array = parent::toArray();

        $serializeData = [
            'id' => $array['id'],
            'headId' => $array['headprodid'],
            'productId' => $array['productoid'],
            'reference' => $array['referencia'],
            'name' => $array['nombre'],
            'type' => $array['Tipo'],
            'nameSupplier' => $array['nombrprove'],
            'init' => $array['unidad'],
            'inventoryMonth' => $array['inventames'],
            'size' => ['id' => $array['tallaid'], 'name' => $array['nombrtalla']],
            'color' => ['id' => $array['colorid'], 'name' => $array['nombrcolor']],
            'typeProduct' => ['id' => $array['tipoproductoid'], 'name' => $array['nombrtipro']],
            'brand' => ['id' => $array['marcaid'], 'name' => $array['nombrmarca']],
            'state' => $array['estado'],
            'cost' => $array['costo'],
            'averageCost' => $array['costoprome'],
            'presetantionId' => $array['PresentacionId'] ?? null,
            'namePresentation' => $array['namePresentation'] ?? null,
        ];

        foreach (static::$relationsToInclude as $relation) {
            if ($this->relationLoaded($relation)) {
                $serializeData[$relation] = $this->$relation;
            }
        }


        return $serializeData;
    }
}
