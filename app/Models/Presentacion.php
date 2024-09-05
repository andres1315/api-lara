<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presentacion extends Model
{
    use HasFactory;
    protected $table = 'Presentacion';
    protected $primaryKey = 'PresentacionId';
  
    public $timestamps = false;
    

    public function toArray()
    {
        $array = parent::toArray();

        $serializeData = [
            'id'                => $array['PresentationId'],
            'headProdId'        => $array['HeadProdId'],
            'name'              => $array['Nombre'],
            'reference'         => $array['Valor'],
            'name'              => $array['Factor'],
            'type'              => $array['Reemplaza'],
            'nameSupplier'      => $array['Venta'],
            'init'              => $array['DefectoVenta'],
            'inventoryMonth'    => $array['Compra'],
            'state'             => $array['defectoCompra'],
            'cost'              => $array['Inventario'],
            'averageCost'       => $array['DefectoInventario'],
            'averageCost'       => $array['Pasta'],
            'averageCost'       => $array['Blister'],
            'averageCost'       => $array['Estado'],
            'averageCost'       => $array['UnidadDianId'],
        ];



        return $serializeData;
    }
}
