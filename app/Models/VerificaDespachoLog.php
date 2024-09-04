<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificaDespachoLog extends Model
{
    use HasFactory;
    protected $table = 'VerificaDespachoLog';
    protected $primaryKey = 'Id';
    protected $fillable = ['DespachoLogId', 'ProductoId', 'Cantidad','Fecha','LoteProductoId','PresentacionId','Factor'];
    public $timestamps = false;


    public function toArray()
  {
    $array = parent::toArray();

    $serializeData = [
      'id'                  => $array['VerificaDespachoLogId'],
      'dispatchLogId'       => $array['DespachoLogId'],
      'productId'           => $array['ProductoId'],
      'qty'                 => $array['Cantidad'],
      'date'                => $array['Fecha'],
      'loteProduct'         => $array['LoteProductoId'],
      'presentationId'      => $array['PresentacionId'],
      'factor'              => $array['Factor'],

    ];

    return $serializeData;
  }
}
