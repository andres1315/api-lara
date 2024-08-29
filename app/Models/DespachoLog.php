<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DespachoLog extends Model
{
    use HasFactory;
    protected $table = 'DespachoLog';
    protected $primaryKey = 'Id';
    public $timestamps = false;


    public function toArray()
  {
    $array = parent::toArray();

    $serializeData = [
      'id'                      => $array['Id'],
      'movementId'              => $array['MovimientoId'],
      'billId'                  => $array['FacturaId'],
      'date'                    => $array['DechaRegis'],
      'state'                   => $array['Estado'],
      'startDatePicking'        => $array['AlistamientoInicio'],
      'endDatePicking'          => $array['AlistamientoFin'],
      'operatorIdPicking'       => $array['OperarioIdAli'],
      'startDatePacking'        => $array['EmpaqueInicio'],
      'endDatePacking'          => $array['EmpaqueFin'],
      'operatorIdPacking'       => $array['OperarioIdAli'],
      'observation'             => $array['Observacion'],
      'requisitionId'           => $array['RequisicionId'],
      'priority'                => $array['Prioridad'],

    ];

    return $serializeData;
  }
}
