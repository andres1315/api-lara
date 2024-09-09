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
        ->select('DespachoNovedad.*');
    }

    public function detailNewsDispatch(): HasMany{
        return $this->HasMany(DespachoLogNovedad::class, 'NovedadId', 'NovedadId');
    }


    public function scopeWithDetailDispatch(Builder $query){
        return $query->with(['detailNewsDispatch']);
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
