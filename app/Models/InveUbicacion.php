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

    public function bandejas()
    {
        return $this->hasMany(UbicacionBandeja::class, 'BandejaId', 'BandejaId');
    }

    public function primeraBandeja()
    {
        return $this->hasOne(UbicacionBandeja::class, 'BandejaId', 'BandejaId')
            ->orderByDesc('barras');
    }

    public function toArray()
    {
        $array = parent::toArray();
        $serializeData = [
            'id'                  => $array['Id'],
            'warehouseId'         => $array['BandejaId'],
            'productId'           => $array['ProductoId'],
            'lotProductId'        => $array['LoteProductoId'],
            'currentInventory'    => $array['InvenActua'],
            'primeraBandeja'      => $this->primeraBandeja

        ];

        return $serializeData;
    }
}
