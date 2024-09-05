<?php

namespace App\Http\Controllers;

use App\Models\InveUbicacion;
use App\Models\UbicacionBandeja;
use App\Models\VerificaDespachoLog;
use Illuminate\Http\Request;

class InveUbicacionController extends Controller
{
    public function newQtyProductLocation(Request $request){
        $productId = $request->input('product');
        $qty = $request->input('qty');
        $location = $request->input('location');
        $dispatchLogId = $request->input('dispatchLogId');
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


        /*
            ? QUE SE DEBE HACER?
            * 1) RESTAR CANTIDAD DE LA UBICACION,✅
            * 2) INSERTAR MOVIMIENTO EN TABLA MOVIUBICACION,✅
            * 2) AGREGAR EN TABLA VERIFICA DESPACHOLOG EL PRODUCTO . ¿ LA UBICACION DE DONDE SE SACO DONDE SE RELACION?


        */

        $newVerifyDispatchLog = new VerificaDespachoLog();
        $newVerifyDispatchLog->DespachoLogId = $dispatchLogId;
        $newVerifyDispatchLog->ProductoId = $productId;
        $newVerifyDispatchLog->Cantidad = $qty;
        $newVerifyDispatchLog->Fecha = date('Y-m-d H:i:s');
        $newVerifyDispatchLog->save();

        $response['message'] = "success";
        $response['status'] = 200;
        return response()->json($response,200);

    }
}
