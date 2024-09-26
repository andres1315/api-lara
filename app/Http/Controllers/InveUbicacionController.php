<?php

namespace App\Http\Controllers;

use App\Models\DespachoLog;
use App\Models\HeadMovi;
use App\Models\HeadRequ;
use App\Models\InveProd;
use App\Models\InveUbicacion;
use App\Models\Movimi;
use App\Models\MoviUbicacion;
use App\Models\UbicacionBandeja;
use App\Models\VerificaDespachoLog;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Throwable;
use App\Http\Resources\InveProd as InveProdResource;
use App\Models\Document;

class InveUbicacionController extends Controller
{
    public function newQtyProductLocation(Request $request)
    {
        $messageValidator = [
            'dispatchLogId.required'            => 'dispatchLogId es Requerido',
            'warehouseId.required'              => 'warehouseId es Requerido',
            'location.required'                 => 'location es Requerido',
            'product.required'                  => 'product es Requerido',
            'idsDetailsRequisitions.required'   => 'idsDetailsRequisitions es Requerido',
        ];

        $validator = Validator::make($request->all(), [
            'product'                   => 'required',
            'qty'                       => 'required',
            'location'                  => 'required',
            'dispatchLogId'             => 'required',
            'warehouseId'               => 'required',
            'idsDetailsRequisitions'    => 'required',
            'headMoviId'                => 'nullable',
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
        $headMoviId             = $request->input('headMoviId');
        $user                   = (object) $request->get('userAuth');

        $response = [
            'message'   => '',
            'status'    => 400,
        ];

        $foundLocation = UbicacionBandeja::IsActive()->where('Barras', $location)->first();
        if ($foundLocation == null) {
            $response['message'] = "No se encontro la posicion $location";
            return response()->json($response, 400);
        }
        $parseFoundLocation = (object) $foundLocation->toArray();

        $foundProductOnLocation = InveUbicacion::FilterTrayAndProduct($productId, $parseFoundLocation->id,$warehouseId)->first();

        if ($foundProductOnLocation == null) {
            $response['message'] = "No se encontro el producto $productId en la  posicion $location";
            return response()->json($response, 400);
        }
        $parseFoundProductOnLocation = (object) $foundProductOnLocation->toArray();
        $qtyInventoryOnLocation = floatval($parseFoundProductOnLocation->currentInventory) ?? 0;
        if ($qtyInventoryOnLocation < $qty) {
            $roundedQty = $qtyInventoryOnLocation;
            $response['message'] = "La cantidad en la ubicación es menor. Cantidad Actual: {$roundedQty}";
            return response()->json($response, 400);
        }

        $inventoryonInveProd = InveProd::InventoryWarehouse($productId,$warehouseId)->first();

        $qtyInventoryOnInveProd= floatval($inventoryonInveProd->invenactua) ?? 0;
        if($qtyInventoryOnInveProd< $qty){
            $qtyOnInventory = floatval($inventoryonInveProd->invenactua);
            $response['message'] = "La cantidad en el invetario(inveprod) es menor. Cantidad Actual: {$qtyOnInventory}";
            return response()->json($response, 400);

        }

        /* FIND IF EXIST DOCUMENT DC */



        DB::beginTransaction();
        try {
            $response = [
                'message' => 'success',
                'status' => 200
            ];

            // START PICKING
            $consecutiveMovimi = null;
            if(!$headMoviId){
                $documentDispatchCustomer ='DC';
                $documentDC =  Document::where('documentoid',$documentDispatchCustomer)->first();
                if($documentDC->count() == 0){
                    $response['message'] = "No existe el documento DC (Despacho Cliente), crear el documento para continuar con el picking";
                    return response()->json($response, 400);
                }
                $currentConsecutive = $documentDC->consenumer ?? 0;
                $newConsecutiveDocumentDC = $currentConsecutive+1;
                $documentDC->update(['consenumer'=>$newConsecutiveDocumentDC]);
                $consecutiveMovimi = $this->buildConsecutiveDocument($newConsecutiveDocumentDC,$documentDispatchCustomer,$warehouseId);
                $idHeadMovi =HeadMovi::create([
                    'consemovim'    => $consecutiveMovimi,
                    'numero'        => $newConsecutiveDocumentDC,
                    'documentoid'   => $documentDispatchCustomer,
                    'fecha'         => date('Y-m-d'), #now()
                    'almacenid'     => $warehouseId,
                    'fechadigit'    => now()

                ])->movimientoid;

                DespachoLog::where('Estado', 'A')
                ->whereIn('Id',$dispatchLogId)
                ->whereNull('AlistamientoInicio')
                ->update([
                    'AlistamientoInicio'    => now(),
                    'HeadMoviId'            => $idHeadMovi
                ]);

                $headMoviId=$idHeadMovi;
            }else{
                $consecutiveMovimi = HeadMovi::where('movimientoid',$headMoviId)->consemovim;
                if(!$consecutiveMovimi){
                    DB::rollback();
                    $response['message'] = 'No se logro obtener el consecutivo del consemovim';
                    $response['status'] = 400;
                    return response()->json($response, $response['status']);
                }
            }




            /* UPDATE QTY INVENTORY ON LOCATION */
            $newQtyInventory = ($foundProductOnLocation->InvenActua - $qty);
            if($newQtyInventory == 0){
                $foundProductOnLocation->delete();
            }else{
                $foundProductOnLocation->InvenActua = $newQtyInventory;
                $foundProductOnLocation->save();
            }

            /*UPDATE QTY INVENTORY ON INVEPROD */
            $newQtyInventoryInveprod =($inventoryonInveProd->invenactua - $qty);
            $inventoryonInveProd->invenactua =$newQtyInventoryInveprod;
            $inventoryonInveProd->save();

            $resultGroup = $this->getDispatchToGroupRequisitions($idsDetailsRequisitions,$dispatchLogId,$qty);

            if(!$resultGroup['success']){
                DB::rollback();
                $response['message'] = $resultGroup['message'];
                $response['status'] = 400;
                return response()->json($response, $response['status']);

            }

            $itemsVerifyDispatchLog = $resultGroup['data'];

            foreach ($itemsVerifyDispatchLog as $key => $item) {
                $idMovimi= Movimi::create([
                    'movimientoid'  => $headMoviId,
                    'consemovim'    => $consecutiveMovimi,
                    'productoid'    => $productId,
                    'cantidad'      => $item["qty"],

                ])->id;
                VerificaDespachoLog::create([
                    'DespachoLogId'         => $item["dispatchLogId"],
                    'ProductoId'            => $productId,
                    'Cantidad'              => $item["qty"],
                    'Fecha'                 => date('Y-m-d'),
                    'Tipo'                  => 'A',
                    'RequisicionDetalleId'  => $item["detailIdRequisition"],
                    'MovimiId'              => $idMovimi

                ]);

                $outputMovement = 'S';
                $trayId = $parseFoundLocation->id;
                $wareHouseId = $parseFoundLocation->forniture->AlmacenId;

                MoviUbicacion::create([
                    'Fecha' => date('Y-m-d'),
                    'TipoMovimiento' => $outputMovement,
                    'BandejaId' => $trayId,
                    'ProductoId' => $productId,
                    'Cantidad' => $item["qty"],
                    'FechaRegistro' => now(),
                    'AlmacenId' => $wareHouseId,
                    'OperarioId' => $user->operarioid,
                ]);
            }

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

    private function buildConsecutiveDocument(int $conse, string $document, string $almacen) {
		$cantFaltante = 19 - (strlen($conse) + strlen($document) + strlen(trim($almacen)));
		$ceros = '';
		for ($i=0; $i < $cantFaltante; $i++) {
			$ceros .= '0';
		}
		return $document . $ceros . trim($almacen) . "-" . $conse;
	}
}
