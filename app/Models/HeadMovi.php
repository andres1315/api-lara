<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeadMovi extends Model
{
    use HasFactory;
    protected $table = 'HeadMovi';
    protected $primaryKey = 'movimientoid';
    public $timestamps = false;

    protected $fillable = ['numero', 'documentoid', 'fecha','almacenid','usuarioid','consemovim','fechadigit','fechavence','terceroid','FechaConta','fletes','ivafletes','retencion','porcereten','reteniva','retenica','ajustpeso','ajustiva','descuento','descufinan','seguro','ivaseguro','PorcentajeAIUBase','AIUBase','PorcentajeUtilidad','Utilidad','IvaIdAIU','IvaAIU','SinImpuRete','Especial'];
}
