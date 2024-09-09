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


    public function toArray(){
        $array = parent::toArray();

        $serializeData = [
            'id'                => $array['PresentationId'],
            'headProdId'        => $array['HeadProdId'],
            'name'              => $array['Nombre'],
            'value'             => $array['Valor'],
            'factor'            => $array['Factor'],
            'replace'           => $array['Reemplaza'],
            'sale'              => $array['Venta'],
            'salereDefect'      => $array['DefectoVenta'],
            'buy'               => $array['Compra'],
            'buyDefect'         => $array['defectoCompra'],
            'inventory'         => $array['Inventario'],
            'inventoryDefect'   => $array['DefectoInventario'],
            'pill'              => $array['Pasta'],
            'blister'           => $array['Blister'],
            'state'             => $array['Estado'],
            'undDIANId'         => $array['UnidadDianId'],
        ];
        return $serializeData;
    }
}
