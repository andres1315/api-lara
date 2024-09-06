<?php

namespace App\Http\Controllers;

use App\Models\HeadRequ;
use Illuminate\Http\Request;


class RequisicionController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = (object) $request->get('userAuth');

        $requisitions = HeadRequ::ApprovedAndAssigned($user->operarioid)
            ->withRelations()->get();
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
        $requisitionArray = $requisition->toArray();
        return response()->json(['requisitionData' => $requisitionArray]);
    }
}
