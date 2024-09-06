<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbicacionBandeja extends Model
{
    use HasFactory;
    protected $table = 'UbicacionBandeja';
    protected $primaryKey = 'BandejaId';

    public $timestamps = false;

    public function scopeIsActive(Builder $query){
        return $query->join('UbicacionMueble','UbicacionMueble.MuebleId','=','UbicacionBandeja.MuebleId')
        ->where('UbicacionBandeja.Estado', 'A')
        ->where('UbicacionMueble.Estado', 'A')
        ->select('UbicacionBandeja.*','UbicacionMueble.AlmacenId');
    }

    public function toArray()
    {
        $array = parent::toArray();
        $serializeData = [
            'id'                  => $array['BandejaId'],
            'furnitureid'         => $array['MuebleId'],
            'description'         => $array['Descripcion'],
            'state'               => $array['Estado'],
            'blockInventorySell'  => $array['BloqueaInventarioVentas'],
            'barCode'             => $array['Barras'],
            'capacity'            => $array['Capacidad'],
            'warehouseId'         => @$array['AlmacenId'],

        ];

        return $serializeData;
    }
}
