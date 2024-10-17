<?php

namespace App\Models;

use App\Enums\Priority;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

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
    ->join('Operario',function($join){
        $join->on('DespachoLog.OperarioIdAli','=','Operario.Operarioid')
        ->whereColumn('HeadRequ.BodegaId','=','Operario.AlmacenId');
    })
    ->where('HeadRequ.Estado', '!=', 'NU')
   ->where('HeadRequ.Aprobada','S')
   ->where('DespachoLog.OperarioIdAli',$user_id)
   ->where('DespachoLog.Estado','A')
   ->whereNull('DespachoLog.Alistamientofin')
   ->whereNull('DespachoLog.GrupoRQ')
   ->select('HeadRequ.*','DespachoLog.GrupoRQ','DespachoLog.IdHeadMovi','DespachoLog.CodigoCanasta','DespachoLog.Prioridad AS PriorityDispatch')
   ->orderBy('HeadRequ.Prioridad', 'asc');
  }


  public function scopeWithDispatchLog(Builder $query){
    $relations = ['dispatchLog'];
    static::$relationsToInclude = array_merge( static::$relationsToInclude,$relations);
    return $query->with($relations);
  }


  public function scopeWithRelations(Builder $query)
  {
    $relations = ['warehouse'];
    static::$relationsToInclude = array_merge( static::$relationsToInclude,$relations);
    return $query->with($relations);
  }

  public function scopeWithDetailRequisition(Builder $query){
    $relations = ['requDetail'];
    static::$relationsToInclude = array_merge( static::$relationsToInclude,$relations);
    return $query->with($relations);
  }

  public function scopeWithDetailRequisitionGroup(Builder $query){
    $relations = ['requDetail'];
    static::$relationsToInclude = array_merge( static::$relationsToInclude,$relations);
    return $query->with(['requDetail'=> function($query){
        $query->groupedProducts();
    }]);
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

  public function scopeApprovedAndAssignedGroup(Builder $query,$user_id)
  {
   return $query->join('DespachoLog','DespachoLog.RequisicionId','=','HeadRequ.RequisicionId')
   ->join('Operario',function($join){
        $join->on('DespachoLog.OperarioIdAli','=','Operario.Operarioid')
        ->whereColumn('HeadRequ.BodegaId','=','Operario.AlmacenId');
    })
   ->where('HeadRequ.Estado', '!=', 'NU')
   ->where('HeadRequ.Aprobada','S')
   ->where('DespachoLog.OperarioIdAli',$user_id)
   ->where('DespachoLog.Estado','A')
   ->whereNull('DespachoLog.Alistamientofin')
   ->whereNotNull('DespachoLog.GrupoRQ')
   ->select('HeadRequ.*', 'DespachoLog.GrupoRQ','DespachoLog.IdHeadMovi','DespachoLog.CodigoCanasta','DespachoLog.Prioridad AS PriorityDispatch')
   ->orderBy('HeadRequ.Prioridad', 'asc');
  }

  public function scopeRequisitionDetailById(Builder $query, array $idsDetailRequisition)
  {
   return $query->join('Requisicion','Requisicion.RequisicionId','=','HeadRequ.RequisicionId')
   ->join('DespachoLog','DespachoLog.RequisicionId','=','HeadRequ.RequisicionId')
   ->where('HeadRequ.Estado', '!=', 'NU')
   ->where('HeadRequ.Aprobada','S')
   ->where('Requisicion.NoPendiente',0)
   ->whereIn('Requisicion.id',$idsDetailRequisition)
   ->whereNotNull('DespachoLog.GrupoRQ')
   ->select('Requisicion.id as detailRequisitionId','Requisicion.RequisicionId','Requisicion.Aprobados as approved','Requisicion.ProductoId','Requisicion.Factor','Requisicion.PresentacionId','DespachoLog.Id as dispatchLogId','DespachoLog.Prioridad AS PriorityDispatch')
   ->orderBy('HeadRequ.Prioridad', 'asc');
  }




  public function toArray()
  {
    $array = parent::toArray();



    $serializeData = [
      'id'                  => [$array['RequisicionId']],
      'consecutive'         => [$array['ConseRequi']],
      'date'                => $array['Fecha'],
      'warehouseId'         => $array['BodegaId'],
      'approvalDate'        => $array['FechaAprobacion'],
      'approved'            => $array['Aprobada'],
      'priority'            => ['id' => (int) $array['PriorityDispatch'], 'text' => Priority::from($array['PriorityDispatch'])->name],  // 3->normal, 2->medio, 1->urgente
      'groupRQ'             => $array['GrupoRQ'] ?? null,
      'headMoviId'          => $array['IdHeadMovi'] ?? null,
      'basketCode'          => $array['CodigoCanasta'] ?? null,
    ];


    foreach (static::$relationsToInclude as $relation) {
      if ($this->relationLoaded($relation)) {
        $serializeData[$relation] = $this->$relation;
      }
    }



    return $serializeData;
  }
}
