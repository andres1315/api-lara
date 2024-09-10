<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DespachoNovedad extends Model
{
    use HasFactory;

    protected $table = 'DespachoNovedad';
    protected $primaryKey = 'NovedadId';
    public $timestamps = false;

    public function scopeActiveAndFilterType(Builder $query,$type){
        return $query->join('DespachoLogNovedad','DespachoLogNovedad.NovedadId','=','DespachoNovedad.NovedadId')
        ->where([
            ['DespachoNovedad.Estado','=', 'A'],
            ['DespachoLogNovedad.Tipo','=', $type],
        ])
        ->select('DespachoNovedad.*')
        ->groupBy('DespachoNovedad.NovedadId','DespachoNovedad.Nombre','DespachoNovedad.Estado');
    }

    public function detailNewsDispatch(): HasMany{
        return $this->HasMany(DespachoLogNovedad::class, 'NovedadId', 'NovedadId');
    }


    public function scopeWithDetailDispatch(Builder $query,$dispatchLogId): Builder{
        return $query->with(['detailNewsDispatch'=> function($query) use($dispatchLogId){
            $query->where('DespachoLogId','=', $dispatchLogId);
        }]);
    }
/*
    public function toArray(){
        $array = parent::toArray();

        $serializeData = [
            'id'        => $array['NovedadId'],
            'name'      => $array['Nombre'],
            'state'     => $array['Estado'],
        ];
        return $serializeData;
    } */
}
