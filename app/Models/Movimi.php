<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimi extends Model
{
    use HasFactory;

    protected $table = 'Movimi';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['movimientoid','consemovim', 'productoid', 'cantidad','costo','costodescu','costoreal','costototal','costoorigi','iva','ivaid'];
}
