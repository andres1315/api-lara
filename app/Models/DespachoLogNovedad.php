<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DespachoLogNovedad extends Model
{
    use HasFactory;


    protected $table = 'DespachoLogNovedad';
    protected $primaryKey = 'Id';
    public $timestamps = false;


    public function scopeActiveAndFilterType(Builder $query,$type){
        return $query->join('DespachoNovedad','DespachoLogNovedad.NovedadId','=','DespachoNovedad.NovedadId')
        ->where([
            ['DespachoNovedad.Estado','=', 'A'],
            ['DespachoLogNovedad.Tipo','=', $type],
        ])
        ->select('DespachoLogNovedad.*');
    }

    public function headNewsDispatch(): BelongsTo{
        return $this->BelongsTo(DespachoNovedad::class, 'NovedadId', 'NovedadId');
    }


    public function scopeWithHeadDispatch(Builder $query){
        return $query->with(['headNewsDispatch']);
    }
/*
    public function toArray(){
        $array = parent::toArray();

        $serializeData = [
            'id'                => $array['Id'],
            'dispatchLogId'     => $array['DespachoLogId'],
            'date'              => $array['Fecha'],
            'type'              => $array['Tipo'],
        ];
        return $serializeData;
    } */
}
