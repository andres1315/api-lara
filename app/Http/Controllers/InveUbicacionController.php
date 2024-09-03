<?php

namespace App\Http\Controllers;

use App\Models\InveUbicacion;
use App\Models\UbicacionBandeja;
use Illuminate\Http\Request;

class InveUbicacionController extends Controller
{
    public function newQtyProductLocation(Request $request){
        $productId = $request->input('product');
        $qty = $request->input('qty');
        $location = $request->input('location');

        $foundLocation = UbicacionBandeja::IsActive()
        ->where('Barras', $location)->first();
        if( $foundLocation == null ){
            return response()->json(['location' => $foundLocation,'message'=>"No se encontro la posicion $location"],400);
        }
        $parseFoundLocation =  (object)$foundLocation->toArray();

        $foundProductOnLocation = InveUbicacion::FilterTrayAndProduct($productId,$parseFoundLocation->id)->first();
        if( $foundProductOnLocation == null ){
            return response()->json(['prodOnLocation' => $foundProductOnLocation,'message'=>"No se encontro la posicion $parseFoundLocation->id  y producto $productId",'foundLocation'=>$foundLocation],400);
        }
        $parseFoundProductOnLocation =  (object) $foundProductOnLocation->toArray();
        if( round($parseFoundProductOnLocation->currentInventory) < $qty   ){
            return response()->json(['prodOnLocation' => $foundProductOnLocation,'message'=>"No se encontro la posicion  {round($parseFoundProductOnLocation->currentInventory) }"],400);
        }
        return response()->json(['prodOnLocation' => $foundProductOnLocation,'message'=>"No se encontro la posicion $parseFoundProductOnLocation->currentInventory"]);

    }
}
