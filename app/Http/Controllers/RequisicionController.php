<?php

namespace App\Http\Controllers;

use App\Models\DespachoLog;
use App\Models\HeadRequ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class RequisicionController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = (object) $request->get('userAuth');

        $requisitions = HeadRequ::ApprovedAndAssigned($user->operarioid)->withRelations()->get();
        $requisitionArray = $requisitions->map(function ($requisition) {
            return $requisition->toArray();
        });

        return response()->json(['requisition' => $requisitionArray]);
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
            foreach($requisition->requDetail as $requisitionDetail){
                $requisitionDetail->applyPresentationFilter();
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
}
