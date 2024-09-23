<?php

namespace App\Http\Controllers;

use App\Models\DespachoLog;
use App\Models\HeadRequ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class RequisicionController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = (object) $request->get('userAuth');

        $requisitions = HeadRequ::ApprovedAndAssigned($user->operarioid)->withRelations()->get();
    
        $requisitionGroup = HeadRequ::ApprovedAndAssignedGroup($user->operarioid)->get();

        return response()->json(['requisition' => $requisitionGroup]);
    }

    public function show(string $id, Request $request)
    {
        $user = (object) $request->get('userAuth');
        $requisition = HeadRequ::ApprovedAndAssigned($user->operarioid)
            ->withRelations()
            ->withDetailRequisition()
            ->withDispatchLogDetail()
            ->where('HeadRequ.RequisicionId', $id)
            ->firstOrFail();
            $warehouseRq = $requisition->BodegaId;
            foreach($requisition->requDetail as $requisitionDetail){

                $requisitionDetail->withSuggestedLocationProducts($warehouseRq);
            }
        $requisitionArray = $requisition->toArray();
        return response()->json(['requisitionData' => $requisitionArray]);
    }


    public function toFinishRequisition(Request $request, string $id){
        DB::beginTransaction();
        $response=[
            'message' =>'success',
            'status' =>200
        ];
        try{
            $dispatchLog  = DespachoLog::find( $id );
            if( $dispatchLog->Id){
                $dispatchLog->AlistamientoFin = now();
                $dispatchLog->save();
            }
            DB::commit();
            return response()->json($response,$response['status']);

        }catch(Throwable $th){
            DB::rollBack();
            $response['status']=400;
            $response['message']= $th->getMessage();
            return response()->json($response,$response['status']);
        }
    }

    public function createGroupRequisition(Request $request){
        /*
        ? SE PUEDE AGRUPAR SI LA RQ YA TIENE AVANCE

        */


        $messageValidator = [
            'requisitionToGroup.required'   => 'requisitionToGroup es Requerido'
        ];

        $validator = Validator::make($request->all(), [
            'requisitionToGroup' => 'required'
        ],$messageValidator);

        if ($validator->fails()) {
            $response['message'] =$validator->errors();
            $response['success'] =false;
            $response['status'] =400;
            return  response()->json($response, 400);
        }
        $user = (object) $request->get('userAuth');
        $ids_requisitions= $request->input('requisitionToGroup');
        $requisitionFreeToGroup = HeadRequ::ApprovedAndAssigned($user->operarioid)->whereIn('HeadRequ.RequisicionId',$ids_requisitions)->get();
        $arratnew=[];
        if($requisitionFreeToGroup->count() != count($ids_requisitions)){
            $response =[
                'success'   => false,
                'message'   => "Las requisiciones ".implode(',',$ids_requisitions)." no puede agregarse a un grupo",
                'status'    => 400
            ];
            

            if($requisitionFreeToGroup->count()==0)return response()->json($response,400);
            $filtersRequ = array_column(array_filter($requisitionFreeToGroup->toArray(), function($rq) use ($ids_requisitions){
                return in_array($rq['id'],$ids_requisitions);
            }),'id');
            
            $response['message'] = "Las requisiciones ".implode(',',$filtersRequ)." no puede agregarse a un grupo";
            return response()->json($response,400);
            
        }


        $dispatchLog = DespachoLog::whereIn('RequisicionId',$ids_requisitions)->get();
        DB::beginTransaction();
        try{

            foreach ($dispatchLog as $key => $dispatch) {
                $dispatch->GrupoRq =$ids_requisitions[0];
                
                $dispatch->save();
            }
            DB::commit();
            return response()->json(["group"=>$dispatchLog],200);
        }catch(Throwable $th){
            DB::rollback();
            $response=[
                "success"   => false,
                "message"   => $th->getMessage(),
            ];
            return response()->json($response,400);

        }


    }

    private function getDetailRequisitionGroup(){
        
    }
}
