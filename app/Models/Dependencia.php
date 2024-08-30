<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dependencia extends Model{
  use HasFactory;

  protected $table = 'Dependencia';
  protected $primaryKey = 'Id';

  public $timestamps = false;

  public function toArray(){
    $array = parent::toArray();


    $serializeData = [
      'id'            => $array['Id'],
      'dependencyId'  => $array['DependenciaId'],
      'name'          => $array['Nombre'],
      'state'         => $array['Estado'],
      'alertPQR'      => $array['AlertasPQR'],
      'costCenter'    => $array['CentCostId'],


    ];

    return $serializeData;
  }
}
