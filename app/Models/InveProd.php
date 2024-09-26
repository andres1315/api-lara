<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InveProd extends Model
{
    use HasFactory;
    protected $table = 'InveProd';
    protected $primaryKey = 'InveProdId';

    public $timestamps = false;


    public function scopeInventoryWarehouse(Builder $query, string $productId, string $warehouseId){
        return $query->where([
            ['productoid','=',$productId],
            ['almacenid','=',$warehouseId],
        ])
        ->select('invenactua','productoid','almacenid','InveProdId');
    }

}
