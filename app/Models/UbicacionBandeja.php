<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UbicacionBandeja extends Model
{
    use HasFactory;
    protected $table = 'UbicacionBandeja';
    protected $primaryKey = 'BandejaId';

    public $timestamps = false;

    public function scopeIsActive(Builder $query){
        return $query->where('Estado', 'A');
    }

    public function forniture(): BelongsTo{
        return $this->belongsTo(UbicacionMueble::class,'MuebleId','MuebleId');
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
            'forniture'           => $this->forniture

        ];

        return $serializeData;
    }
}
