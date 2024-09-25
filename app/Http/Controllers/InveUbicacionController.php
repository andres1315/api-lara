<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewQtyProducLocationRequest;
use App\Models\DespachoLog;
use App\Models\HeadRequ;
use App\Models\InveUbicacion;
use App\Models\MoviUbicacion;
use App\Models\UbicacionBandeja;
use App\Models\VerificaDespachoLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Type\Integer;
use Throwable;

class InveUbicacionController extends Controller
{
    public function newQtyProductLocation(Request $request)
    {
        $messageValidator = [
            'dispatchLogId.required'            => 'dispatchLogId es Requerido',
            'warehouseId.required'              => 'warehouseId es Requerido',
            'location.required'                 => 'location es Requerido',
            'product.required'                  => 'product es Requerido',
            'idsDetailsRequisitions.required'   => 'idsDetailsRequisitions es Requerido'

        ];

        $validator = Validator::make($request->all(), [
            'product'                   => 'required',
            'qty'                       => 'required',
            'location'                  => 'required',
            'dispatchLogId'             => 'required',
            'warehouseId'               => 'required',
            'idsDetailsRequisitions'    => 'required'
        ],$messageValidator);

        if ($validator->fails()) {
            $response['message'] =$validator->errors();
            $response['success'] =false;
            $response['status'] =400;
            return  response()->json($response, 400);
        }

        $productId              = $request->input('product');
        $qty                    = $request->input('qty');
        $location               = $request->input('location');
        $dispatchLogId          = $request->input('dispatchLogId');
        $warehouseId            = $request->input('warehouseId');
        $idsDetailsRequisitions = $request->input('idsDetailsRequisitions');
        $user                   = (object) $request->get('userAuth');

        $response = [
            'message'   => '',
            'status'    => 200,
        ];

        $foundLocation = UbicacionBandeja::IsActive()->where('Barras', $location)->first();
        if ($foundLocation == null) {
            $response['message'] = "No se encontro la posicion $location";
            $response['status'] = 400;
            return response()->json($response, 400);
        }
        $parseFoundLocation = (object) $foundLocation->toArray();

        $foundProductOnLocation = InveUbicacion::FilterTrayAndProduct($productId, $parseFoundLocation->id,$warehouseId)->first();

        if ($foundProductOnLocation == null) {
            $response['message'] = "No se encontro el producto $productId en la  posicion $location";
            $response['status'] = 400;
            return response()->json($response, 400);
        }
        $parseFoundProductOnLocation = (object) $foundProductOnLocation->toArray();
        if (round($parseFoundProductOnLocation->currentInventory) < $qty) {
            $roundedQty = round($parseFoundProductOnLocation->currentInventory);
            $response['message'] = "La cantidad en la ubicación es menor. Cantidad Actual: {$roundedQty}";
            $response['status'] = 400;
            return response()->json($response, 400);
        }

        DB::beginTransaction();
        try {
            $response = [
                'message' => 'success',
                'status' => 200
            ];


            /* UPDATE QTY INVENTORY ON LOCATION */
            $newQtyInventory = ($foundProductOnLocation->InvenActua - $qty);
            $foundProductOnLocation->InvenActua = $newQtyInventory;
            $foundProductOnLocation->save();



            $resultGroup = $this->getDispatchToGroupRequisitions($idsDetailsRequisitions,$dispatchLogId,$qty);

            if(!$resultGroup['success']){
                DB::rollback();
                $response['message'] = $resultGroup['message'];
                $response['status'] = 400;
                return response()->json($response, $response['status']);

            }

            $itemsVerifyDispatchLog = $resultGroup['data'];

            foreach ($itemsVerifyDispatchLog as $key => $item) {
                /* START DISPATCH */
                $isDispathWithoutStart = DespachoLog::where([
                    ['Estado', '=', 'A'],
                    ['Id', '=', $item["dispatchLogId"]],

                ])->whereNull('AlistamientoInicio')->first();
                if ($isDispathWithoutStart != null) {
                    $isDispathWithoutStart->AlistamientoInicio = now();
                    $isDispathWithoutStart->save();
                }


                VerificaDespachoLog::create([
                    'DespachoLogId'         => $item["dispatchLogId"],
                    'ProductoId'            => $productId,
                    'Cantidad'              => $item["qty"],
                    'Fecha'                 => date('Y-m-d'),
                    'Tipo'                  => 'A',
                    'RequisicionDetalleId'  => $item["detailIdRequisition"]

                ]);
            }
            $outputMovement = 'S';
            $trayId = $parseFoundLocation->id;
            $wareHouseId = $parseFoundLocation->forniture->AlmacenId;

            MoviUbicacion::create([
                'Fecha' => date('Y-m-d'),
                'TipoMovimiento' => $outputMovement,
                'BandejaId' => $trayId,
                'ProductoId' => $productId,
                'Cantidad' => $qty,
                'FechaRegistro' => now(),
                'AlmacenId' => $wareHouseId,
                'OperarioId' => $user->operarioid,
            ]);

            DB::commit();

            return response()->json($response, $response['status']);
        } catch (Throwable $th) {
            DB::rollback();
            $response['message'] = $th->getMessage();
            $response['status'] = 400;
            return response()->json($response, $response['status']);
        }


    }

    public function filterProducts(Request $request){
        try{

            $messageValidator = [
                'find.required'    => 'find es Requerido',
            ];

            $validator = Validator::make($request->all(), [
                'find'       => 'required'
            ],$messageValidator);

            if ($validator->fails()) {
                $response['message'] =$validator->errors();
                $response['success'] =false;
                $response['status'] =400;
                return  response()->json($response, 400);
            }

            $filterText = strtoupper($request->input('find'));
            $user = (object) $request->get('userAuth');

            $result = InveUbicacion::withFilteredProductsAndLocations($filterText)
            ->WithPictureProduct()
            ->withTray()
            ->get();
            return response()->json($result,200);
        }catch(Throwable $th){

        }
    }

    private function getDispatchToGroupRequisitions(array $idGroupRequisitions=[],array $dispatchLogId=[], int $qtyRequest){
        try{
            $result = [
                "success"   => true,
                "data"      => []
            ];
            $isGroupRequisition = count($idGroupRequisitions) > 1;
            if($isGroupRequisition){
                $restQtyRequest = $qtyRequest;
                $requisition = HeadRequ::RequisitionDetailById($idGroupRequisitions)->get();

                foreach ($requisition as  $detail) {
                    $qtyPicking = DB::table('VerificaDespachoLog')
                    ->where('RequisicionDetalleId', $detail->detailRequisitionId)
                    ->sum('Cantidad');

                    $detail->qtyPicking = $qtyPicking;

                    if($detail->qtyPicking < $detail->approved && $restQtyRequest > 0){
                        $remainingQty= floatval($detail->approved) - floatval($detail->qtyPicking);
                        $qtyToMinus = $restQtyRequest <= $remainingQty ? $restQtyRequest : $remainingQty;
                        $result['data'][]=[
                            "dispatchLogId"         => $detail->dispatchLogId,
                            "detailIdRequisition"   => $detail->detailRequisitionId,
                            "qty"                   => $qtyToMinus
                        ];
                        $restQtyRequest -= $qtyToMinus;

                    }
                }


            }else{
                $result['data'][]=[
                    "dispatchLogId"         => $dispatchLogId[0],
                    "detailIdRequisition"   => $idGroupRequisitions[0],
                    "qty"                   => $qtyRequest
                ];
            }

            return $result;
        }catch(Throwable $th){
            return [
                'success'   => false,
                'message'   => $th,
            ];
        }
    }
}
