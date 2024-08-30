<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbicacionBandeja extends Model
{
    use HasFactory;
    protected $table = 'UbicacionBandeja';
    protected $primaryKey = 'BandejaId';

    public $timestamps = false;

    public function toArray()
    {
        $array = parent::toArray();
        $serializeData = [
            'id'                  => $array['BandejaId'],
            'warehouseId'         => $array['MuebleId'],
            'description'         => $array['Descripcion'],
            'state'               => $array['Estado'],
            'blockInventorySell'  => $array['BloqueaInventarioVentas'],
            'barCode'             => $array['Barras'],

        ];

        return $serializeData;
    }
}
