<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
  protected static $relationsToInclude = [];


  public function requDetail(): HasMany
  {
    return $this->hasMany(Requisicion::class, 'RequisicionId', 'RequisicionId')->where('Requisicion.NoPendiente','=',0);
  }
  public function userRequest(): HasOne
  {
    return $this->HasOne(Segur::class, 'usuarioId', 'SolicitanteId');
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

  public function dispatchLog(): HasOne
  {
    return $this->HasOne(DespachoLog::class, 'RequisicionId', 'RequisicionId');
  }


  public function scopeApprovedAndAssigned(Builder $query,$user_id)
  {
   return $query->join('DespachoLog','DespachoLog.RequisicionId','=','HeadRequ.RequisicionId')
   ->where('HeadRequ.Estado', '!=', 'NU')
   ->where('HeadRequ.Aprobada','S')
   ->where('DespachoLog.OperarioIdAli',$user_id)
   ->where('DespachoLog.Estado','A')
   ->whereNull('DespachoLog.Alistamientofin')
   ->whereNull('DespachoLog.GrupoRq')
   ->select('HeadRequ.*')
   ->orderBy('HeadRequ.Prioridad', 'asc');
  }


  public function scopeWithDispatchLog(Builder $query){
    $relations = ['dispatchLog'];
    static::$relationsToInclude = array_merge( static::$relationsToInclude,$relations);
    return $query->with($relations);
  }


  public function scopeWithRelations(Builder $query)
  {
    $relations = ['userRequest', 'warehouse', 'dependency'];
    static::$relationsToInclude = array_merge( static::$relationsToInclude,$relations);
    return $query->with($relations);
  }

  public function scopeWithDetailRequisition(Builder $query){
    $relations = ['requDetail'];
    static::$relationsToInclude = array_merge( static::$relationsToInclude,$relations);


    return $query->with(['requDetail']);
  }


  public function scopeWithDispatchLogDetail(Builder $query){


    $relations = ['dispatchLog'];
    static::$relationsToInclude = array_merge( static::$relationsToInclude,$relations);
    return $query->with([
        'dispatchLog'=> function($query){
            $query->with([
                'verifyDispatchLog'
            ]);
        }
    ]);



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
      'id'                  => $array['RequisicionId'],
      'consecutive'         => $array['ConseRequi'],
      'type'                => $array['Tipo'],
      'date'                => $array['Fecha'],
      'digitDate'           => $array['FechaDigit'],
      'storeId'             => $array['AlmacenId'],
      'userRequestId'       => $array['SolicitanteId'],
      'dependencyId'        => $array['DependenciaId'],
      'warehouseId'         => $array['BodegaId'],
      'approvalDate'        => $array['FechaAprobacion'],
      'approved'            => $array['Aprobada'],
      'incidenceId'         => $array['IncidenciaId'],
      'special'             => $array['Especial'],
      'priority'            => ['id' => (int) $array['Prioridad'], 'text' => $priorityName[$array['Prioridad']]],  // 3->normal, 2->medio, 1->urgente
    ];


    foreach (static::$relationsToInclude as $relation) {
      if ($this->relationLoaded($relation)) {
        $serializeData[$relation] = $this->$relation;
      }
    }



    return $serializeData;
  }
}
