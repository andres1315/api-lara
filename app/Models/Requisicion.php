<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisicion extends Model{
  use HasFactory;

  protected $table = 'HeadRequ';
  protected $primaryKey = 'RequisicionId';
  public $timestamps = false;


  public function toArray(){
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
      'noPending'           => $array['NoPendientes'],
      'observationProduct'  => $array['ObserProdu'],
      'productRequest'      => $array['ProduSolic'],
      'qtyRequest'          => $array['CantiSolic'],
      'typeEndId'           => $array['TipoFinzalizaacionId'],
      'presentationId'      => $array['PresentacionId'],
      'factor'              => $array['Factor'],
      'deliveryDate'        => $array['FechaEntrega'],
      'endDate'             => $array['FechaFinal'],
      'userFId'             => $array['UsuarioFId'],
      'storeOCId'           => $array['AlmacenIdOC'],
      'purchaseOrder'       => $array['OrdenCompr'],
      'incidencePId'        => $array['IncidenciaIdP'],

    ];

    return $serializeData;
  }
}



