<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Requisicion extends Model
{
  use HasFactory;

  protected $table = 'Requisicion';
  protected $primaryKey = 'Id';
  public $timestamps = false;

  public function RequHead(): BelongsTo
  {
    return $this->BelongsTo(HeadRequ::class, 'RequisicionId', 'RequisicionId');
  }

  public function product(): HasOne
  {
    return $this->HasOne(Producto::class, 'productoid', 'ProductoId');

  }

  public function scopeWithProductLocations(Builder $query, $includeLocations = false)
{
    $query->when($includeLocations, function ($query) {
        $query->with(['product' => function ($query) {
            $query->withUbicacionSugerida();
        }]);
    });

    return $query;
}


  public function toArray()
  {
    $array = parent::toArray();


    $serializeData = [
      'id'                  => $array['Id'],
      'requisitionId'       => $array['RequisicionId'],
      'productId'           => $array['ProductoId'],
      'cost'                => $array['Costo'],
      'qty'                 => $array['Cantidad'],
      'iva'                 => $array['Iva'],
      'ivaId'               => $array['IvaId'],
      'approved'            => $array['Aprobados'],
      'approvedDate'        => $array['FechaAprob'],
      'userAprrovedId'      => $array['AprobadorId'],
      'received'            => $array['Recibidos'],
      'noPending'           => $array['NoPendiente'],
      'observationProduct'  => trim($array['ObserProdu']),
      'productRequest'      => $array['ProduSolic'],
      'qtyRequest'          => $array['CantiSolic'],
      'typeEndId'           => $array['TipoFinalizacionId'],
      'presentationId'      => $array['PresentacionId'],
      'factor'              => $array['Factor'],
      'deliveryDate'        => $array['FechaEntrega'],
      'endDate'             => $array['FechaFinal'],
      'userFId'             => $array['UsuarioFId'],
      'storeOCId'           => $array['AlmacenIdOC'],
      'purchaseOrder'       => $array['OrdenCompr'],
      'incidencePId'        => $array['IncidenciaIdP'],
      'productDetail'       => $this->product

    ];

    return $serializeData;
  }
}



