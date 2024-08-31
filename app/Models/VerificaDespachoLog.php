<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificaDespachoLog extends Model
{
    use HasFactory;
    protected $table = 'VerificaDespachoLog';
    protected $primaryKey = 'Id';
    public $timestamps = false;


    public function toArray()
  {
    $array = parent::toArray();

    $serializeData = [
      'id'                      => $array['VerificaDespachoLogId'],
      'movementId'              => $array['DespachoLogId'],
      'billId'                  => $array['ProductoId'],
      'date'                    => $array['Cantidad'],
      'state'                   => $array['Fecha'],
      'startDatePicking'        => $array['LoteProductoId'],
      'endDatePicking'          => $array['PresentacionId'],
      'operatorIdPicking'       => $array['Factor'],

    ];

    return $serializeData;
  }
}
