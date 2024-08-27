<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeadRequ extends Model{
  use HasFactory;
  protected $table = 'HeadRequ';
  protected $primaryKey = 'RequisicionId';

  public $timestamps = false;


  public function toArray(){
    $array = parent::toArray();


    $serializeData = [
      'id'            => $array['RequisicionId'],
      'consecutive'   => $array['ConseRequi'],
      'type'          => $array['Tipo'],
      'date'          => $array['Fecha'],
      'digitDate'     => $array['FechaDigit'],
      'storeId'       => $array['AlmacenId'],
      'userRequestId' => $array['SolicitanteId'],
      'dependency'    => $array['DependenciaId'],
      'warehouseId'   => $array['BodegaId'],
      'approvalDate'  => $array['FechaAprobacion'],
      'approved'      => $array['Aprobada'],
      'incidenceId'   => $array['IncidenciaId'],
      'special'       => $array['Especial'],
      'priority'      => $array['Prioridad'],

    ];

    return $serializeData;
  }
}
