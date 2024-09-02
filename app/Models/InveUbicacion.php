<?php

namespace App\Models;

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
        return $this->hasMany(UbicacionBandeja::class, 'BandejaId', 'BandejaId');
    }

    public function suggestTray()
    {
        $relations = ['suggestTray'];
        static::$relationsToInclude = array_merge(static::$relationsToInclude, $relations);
        return $this->hasOne(UbicacionBandeja::class, 'BandejaId', 'BandejaId')
            ->orderByDesc('barras');
    }

    public function toArray()
    {
        $array = parent::toArray();
        $serializeData = [
            'id'                  => @$array['Id'],
            'warehouseId'         => @$array['BandejaId'],
            'productId'           => @$array['ProductoId'],
            'lotProductId'        => @$array['LoteProductoId'],
            'currentInventory'    => @$array['InvenActua'],

        ];
        foreach (static::$relationsToInclude as $relation) {
            if ($this->relationLoaded($relation)) {
                $serializeData[$relation] = $this->$relation;
            }
        }

        return $serializeData;
    }
}
