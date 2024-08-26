<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dependencia extends Model{
  use HasFactory;

  public function toArray(){
    $array = parent::toArray();


    $serializeData = [
      'id'            => $array['id'],
      'dependencyId'  => $array['DependenciaId'],
      'name'          => $array['Nombre'],
      'state'         => $array['Estado'],
      'alertPQR'      => $array['AlertasPQR'],
      'costCenter'    => $array['CentCostId'],
      'userRequestId' => $array['SolicitanteId'],
      'dependency'    => $array['DependenciaId'],
      'warehouseId'   => $array['BodegaId'],
      'approvalDate'  => $array['FechaAprobacion'],
      'approved'      => $array['Aprobada'],
      'incidenceId'   => $array['incidenciaId'],
      'special'       => $array['Especial'],
      'priority'      => $array['Prioridad'],

    ];

    return $serializeData;
  }
}
