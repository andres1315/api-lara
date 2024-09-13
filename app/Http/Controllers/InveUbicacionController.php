<?php

namespace App\Http\Controllers;

use App\Models\DespachoLog;
use App\Models\InveUbicacion;
use App\Models\MoviUbicacion;
use App\Models\UbicacionBandeja;
use App\Models\VerificaDespachoLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class InveUbicacionController extends Controller
{
    public function newQtyProductLocation(Request $request){
        $productId = $request->input('product');
        $qty = $request->input('qty');
        $location = $request->input('location');
        $dispatchLogId = $request->input('dispatchLogId');
        $user = (object) $request->get('userAuth');
        $response = [
            'message' => '',
            'status'  => 200,
        ];

        $foundLocation = UbicacionBandeja::IsActive()->where('Barras', $location)->first();
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

        DB::beginTransaction();
        try{
            $response=[
                'message'   => 'success',
                'status'    => 200
            ];

            /* START DISPATCH */
            $isDispathWithoutStart = DespachoLog::where([
                ['Estado','=','A'],
                ['Id','=',$dispatchLogId],

            ])->whereNull('AlistamientoInicio')->first();
            if($isDispathWithoutStart != null){
                $isDispathWithoutStart->AlistamientoInicio =date('Y-m-d H:i:s');
                $isDispathWithoutStart->save();
            }
            /* UPDATE QTY INVENTORY ON LOCATION */
            $newQtyInventory = ($foundProductOnLocation->InvenActua-$qty);
            $foundProductOnLocation->InvenActua=$newQtyInventory;
            $foundProductOnLocation->save();

            $outputMovement = 'S';
            $trayId = $parseFoundLocation->id;
            $wareHouseId = $parseFoundLocation->forniture->AlmacenId;
            /**
             *  ?   UsuariodId debe ser un id de la tabla segur, pero en la app se inicia con un operario?
             *  ?   TIpoOrigen char4 que tipo origen es para los movimientos desde la app
             *  ?   NUmeroOrigen para los movimientos de pciking
             *  ?   Que estado debe ser null o vacio ?
             *
             *
            */
            
            MoviUbicacion::create([
                'Fecha'             => date('Y-m-d'),
                'TipoMovimiento'    => $outputMovement,
                'BandejaId'         => $trayId,
                'ProductoId'        => $productId,
                'Cantidad'          => $qty,
                'FechaRegistro'     => now(),
                'UsuarioId'         => 487,//$user->operarioid ,
                'AlmacenId'         => $wareHouseId

            ]);

            /* INSERT LOG DISPATCH */
            VerificaDespachoLog::create([
                'DespachoLogId' => $dispatchLogId,
                'ProductoId' => $productId,
                'Cantidad' => $qty,
                'Fecha' => date('Y-m-d H:i:s')
            ]);

            DB::commit();

            return response()->json($response,$response['status']);
        }catch(Throwable $th){
            DB::rollback();
            $response['message'] =  $th->getMessage();
            $response['status'] = 400;
            return response()->json($response,$response['status']);
        }


    }
}
