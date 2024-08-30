<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbicacionMueble extends Model
{
    use HasFactory;

    protected $table = 'UbicacionMueble';
    protected $primaryKey = 'MuebleId';

    public $timestamps = false;

    public function toArray()
    {
        $array = parent::toArray();
        $serializeData = [
            'id'              => $array['MuebleId'],
            'warehouseId'     => $array['AlmacenId'],
            'description'     => $array['Descripcion'],
            'state'           => $array['Estado'],

        ];

        return $serializeData;
    }
}
