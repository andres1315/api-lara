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
        $response = [
            'message' => '',
            'status'  => 200,
        ];

        $foundLocation = UbicacionBandeja::IsActive()
        ->where('Barras', $location)->first();
        if( $foundLocation == null ){
            $response['message'] = "No se encontro la posicion $location";
            $response['status'] = 400;
            return response()->json($response,400);
        }
        $parseFoundLocation =  (object)$foundLocation->toArray();

        $foundProductOnLocation = InveUbicacion::FilterTrayAndProduct($productId,$parseFoundLocation->id)->first();
        if( $foundProductOnLocation == null ){
            $response['message'] = "No se encontro el producto $productId en la  posicion $location";
            $response['status'] = 400;
            return response()->json($response,400);
        }
        $parseFoundProductOnLocation =  (object) $foundProductOnLocation->toArray();
        if( round($parseFoundProductOnLocation->currentInventory) < $qty   ){
            $roundedQty =round($parseFoundProductOnLocation->currentInventory);
            $response['message'] = "La cantidad en la ubicación es menor. Cantidad Actual: {$roundedQty}";
            $response['status'] = 400;
            return response()->json($response,400);
        }

        $response['message'] = "success";
        $response['status'] = 200;
        return response()->json($response,200);

    }
}
