<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DespachoLog extends Model
{
    use HasFactory;
    protected $table = 'DespachoLog';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = ['Estado', 'AlistamientoInicio', 'AlistamientoFin','IdHeadMovi','GrupoRQ','CodigoCanasta'];

    public function scopeAssignedEnlistment(Builder $query, $user_id)
    {
        $stateEnlistment ='A';
        return $query->where('OperarioIdAli',$user_id)->where('Estado',$stateEnlistment);
    }

    public function verifyDispatchLog(): HasMany
    {
      return $this->HasMany(VerificaDespachoLog::class, 'DespachoLogId', 'Id');

    }


    public function toArray()
  {
    $array = parent::toArray();

    $serializeData = [
      'id'                      => $array['Id'],
      'movementId'              => $array['MovimientoId'],
      'billId'                  => $array['FacturaId'],
      'date'                    => $array['FechaRegis'],
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
      'groupRq'                 => $array['GrupoRQ'],
      'basketCode'              => $array['CodigoCanasta'],
      'headMoviId'              => $array['IdHeadMovi'],
      'verifyDispatchLog'       => $this->verifyDispatchLog

    ];

    return $serializeData;
  }
}
