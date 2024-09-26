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
    public function index(Request $request)
    {
        $user = (object) $request->get('userAuth');
        $requisitionsGroup = $this->getRequisitionGroup(
            HeadRequ::ApprovedAndAssignedGroup($user->operarioid)
            ->withRelations()
            ->get()
            ->toArray()
        );
        $requisitions = HeadRequ::ApprovedAndAssigned($user->operarioid)
        ->withRelations()
        ->get()
        ->toArray();
        $allRequisitions = array_merge($requisitionsGroup, $requisitions);
        return response()->json(['requisition' => $allRequisitions]);
    }

    public function show(string $id, Request $request)
    {
        $user = (object) $request->get('userAuth');
        $requisition = HeadRequ::ApprovedAndAssigned($user->operarioid)
            ->withDetailRequisition()
            ->withDispatchLogDetail()
            ->where('HeadRequ.RequisicionId', $id)
            ->firstOrFail();
        $warehouseRq = $requisition->BodegaId;
        foreach ($requisition->requDetail as $requisitionDetail) {
            $requisitionDetail->withSuggestedLocationProducts($warehouseRq);
        }
        return response()->json(['requisitionData' => $requisition]);
    }


    public function toFinishRequisition(Request $request)
    {
        $messageValidator = [
            'ids.required' => 'ids es Requerido',
            'ids.array' => 'El campo "ids" debe ser un array.',
            'ids.*.required' => 'Cada elemento de "ids" es obligatorio.',
            'ids.*.integer' => 'Cada elemento de "ids" debe ser un número entero.',

        ];

        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',         // Debe ser un array
            'ids.*' => 'required|integer',     // Cada elemento debe ser un número entero
        ], $messageValidator);

        if ($validator->fails()) {
            $response['message'] = $validator->errors();
            $response['success'] = false;
            $response['status'] = 400;
            return response()->json($response, 400);
        }

        $ids = $validator->validated()['ids'];

        DB::beginTransaction();
        $response = [
            'message' => 'success',
            'status' => 200
        ];
        try {
            DespachoLog::whereIn('Id',$ids)->update(['AlistamientoFin'=> now()]);
            /* dd($dispatchLog);
            if ($dispatchLog->Id) {
                $dispatchLog->AlistamientoFin = now();
                $dispatchLog->save();
            } */
            DB::commit();
            return response()->json($response, $response['status']);

        } catch (Throwable $th) {
            DB::rollBack();
            $response['status'] = 400;
            $response['message'] = $th->getMessage();
            return response()->json($response, $response['status']);
        }
    }

    public function createGroupRequisition(Request $request)
    {
        $messageValidator = [
            'requisitionToGroup.required' => 'requisitionToGroup es Requerido'
        ];

        $validator = Validator::make($request->all(), [
            'requisitionToGroup' => 'required'
        ], $messageValidator);

        if ($validator->fails()) {
            $response['message'] = $validator->errors();
            $response['success'] = false;
            $response['status'] = 400;
            return response()->json($response, 400);
        }
        $user = (object) $request->get('userAuth');
        $ids_requisitions = $request->input('requisitionToGroup');
        $requisitionFreeToGroup = HeadRequ::ApprovedAndAssigned($user->operarioid)
        ->whereIn('HeadRequ.RequisicionId', $ids_requisitions)
        ->whereNull('DespachoLog.AlistamientoInicio')
        ->get();
        if ($requisitionFreeToGroup->count() != count($ids_requisitions)) {
            $response = [
                'success' => false,
                'message' => "Las requisiciones " . implode(',', $ids_requisitions) . " no pueden agregarse a un grupo",
                'status' => 400
            ];
            if ($requisitionFreeToGroup->count() == 0) return response()->json($response, 400);
            $idsRequisitionsFreeToGroup  =array_merge(...array_column($requisitionFreeToGroup->toArray(),'id'));
            $idsRequisitionsNoFreeToGroup  = array_diff($ids_requisitions,$idsRequisitionsFreeToGroup);





            $response['message'] = "Las requisiciones " . implode(',', $idsRequisitionsNoFreeToGroup) . " no pueden agregarse a un grupo, ya han sido iniciados o pertenecen a otro grupo";
            return response()->json($response, 400);

        }

        $dispatchLog = DespachoLog::whereIn('RequisicionId', $ids_requisitions)->get();

        DB::beginTransaction();
        try {

            foreach ($dispatchLog as $key => $dispatch) {
                $dispatch->GrupoRq = $ids_requisitions[0];
                $dispatch->save();
            }
            DB::commit();
            return response()->json(["group" => $dispatchLog], 200);
        } catch (Throwable $th) {
            DB::rollback();
            $response = [
                "success" => false,
                "message" => $th->getMessage(),
            ];
            return response()->json($response, 400);

        }


    }

    private function getRequisitionGroup($requisitionOnGroup)
    {

        $newGroups = [];
        foreach ($requisitionOnGroup as $key => $requisition) {
            if (array_key_exists($requisition['groupRQ'], $newGroups)) {
                $newGroups[$requisition['groupRQ']]['id'][] = $requisition['id'][0];
                $newGroups[$requisition['groupRQ']]['consecutive'][] = $requisition['consecutive'][0];
            } else {
                $newGroups[$requisition['groupRQ']] = $requisition;
            }
        }
        return $newGroups;
    }


    public function detailGroup(Request $request)
    {
        $messageValidator = [
            'ids.required' => 'ids es Requerido',
            'ids.array' => 'El campo "ids" debe ser un array.',
            'ids.*.required' => 'Cada elemento de "ids" es obligatorio.',
            'ids.*.integer' => 'Cada elemento de "ids" debe ser un número entero.',

        ];

        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',         // Debe ser un array
            'ids.*' => 'required|integer',     // Cada elemento debe ser un número entero
        ], $messageValidator);

        if ($validator->fails()) {
            $response['message'] = $validator->errors();
            $response['success'] = false;
            $response['status'] = 400;
            return response()->json($response, 400);
        }

        $ids = $validator->validated()['ids'];


        $user = (object) $request->get('userAuth');
        $requisition = HeadRequ::ApprovedAndAssignedGroup($user->operarioid)
            ->withDetailRequisition()
            ->withDispatchLogDetail()
            ->whereIn('HeadRequ.RequisicionId', $ids)
            ->get();

        $groupRequ = new \stdClass();
        $groupRequ->date = $requisition->first()->Fecha;
        $groupRequ->warehouseId = $requisition->first()->BodegaId;
        $groupRequ->approvalDate = $requisition->first()->FechaAprobacion;
        $groupRequ->approved = $requisition->first()->Aprobada;
        $groupRequ->priority = $requisition->first()->Prioridad;
        $groupRequ->groupRQ = $requisition->first()->GrupoRq;
        $groupRequ->basketCode = [];

        $requisition->each(function ($headRequ) use ($groupRequ) {
            $groupRequ->id[] = $headRequ['RequisicionId'];
            $groupRequ->consecutive[] = $headRequ['ConseRequi'];
            $groupRequ->dispatchLog[] = $headRequ['dispatchLog'];
            $headRequ['CodigoCanasta'] && ($groupRequ->basketCode[] = $headRequ['CodigoCanasta']);
        });

        $groupRequ->requDetail = $requisition->flatMap(function ($req) use ($user) {
            foreach ($req->requDetail as $requisitionDetail) {
                $requisitionDetail->withSuggestedLocationProducts($user->warehouseId);
            }
            return $req->requDetail;
        })->groupBy(function ($item) {
            return "{$item->ProductoId}-{$item->PresentacionId}-{$item->Factor}"; // Agrupa por los campos del producto
        })->map(function ($group, $index) {
            return $group->reduce(function ($carry, $item) {
                $carry['id'][] = $item->Id;
                $carry['requisitionId'][] = $item->RequisicionId;
                $carry['approved'] = ($carry['approved'] ?? 0) + $item->Aprobados; // Sumar cantidad Aprobados
                return $carry;
            }, [
                ...$group->first()->toArray(),
                'productId'         => $group->first()->ProductoId,
                'presentationId'    => $group->first()->PresentacionId,
                'factor'            => $group->first()->Factor,
                'id'                => [],
                'requisitionId'     => [],
                'approved'          => 0,
            ]);
        })->values();


        return response()->json(['requisitionData' => $groupRequ]);
    }
}
