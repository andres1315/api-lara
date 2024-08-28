<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HeadRequ extends Model
{
  use HasFactory;
  protected $table = 'HeadRequ';
  protected $primaryKey = 'RequisicionId';

  public $timestamps = false;


  public function requDetail(): HasMany
  {
    return $this->hasMany(Requisicion::class, 'RequisicionId', 'RequisicionId');
  }
  public function userRequest(): HasOne
  {
    return $this->HasOne(User::class, 'usuarioId', 'SolicitanteId');
  }

  public function store(): HasOne
  {
    return $this->HasOne(Almacen::class, 'almacenid', 'AlmacenId');
  }
  public function warehouse(): HasOne
  {
    return $this->HasOne(Almacen::class, 'almacenid', 'BodegaId');
  }

  public function dependency(): HasOne
  {
    return $this->HasOne(Dependencia::class, 'DependenciaId', 'DependenciaId');
  }


  public function toArray()
  {
    $array = parent::toArray();

    $priorityName = [
      1 => 'Urgente',
      2 => 'Medio',
      3 => 'Normal',
    ];

    $serializeData = [
      'id' => $array['RequisicionId'],
      'consecutive' => $array['ConseRequi'],
      'type' => $array['Tipo'],
      'date' => $array['Fecha'],
      'digitDate' => $array['FechaDigit'],
      'storeId' => $array['AlmacenId'],
      'userRequestId' => $array['SolicitanteId'],
      'dependencyId' => $array['DependenciaId'],
      'warehouseId' => $array['BodegaId'],
      'approvalDate' => $array['FechaAprobacion'],
      'approved' => $array['Aprobada'],
      'incidenceId' => $array['IncidenciaId'],
      'special' => $array['Especial'],
      'priority' => ['id' => (int) $array['Prioridad'], 'text' => $priorityName[$array['Prioridad']]],  // 3->normal, 2->medio, 1->urgente
      'detailRQ' => $this->requDetail,
      'userRequest' => $this->userRequest,
      'store' => $this->store,
      'dependency' => $this->dependency,
      'warehouse' => $this->warehouse,
    ];

    return $serializeData;
  }
}
