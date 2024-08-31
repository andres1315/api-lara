<?php

namespace App\Http\Controllers;

use App\Models\HeadRequ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequisicionController extends Controller
{
    //
    public function index(Request $request)
    {
        $requisitions = HeadRequ::ApprovedAndAssigned()
            ->withRelations()
            ->orderBy('Prioridad', 'asc')->get();
        $requisitionArray = $requisitions->map(function ($requisition) {
            return $requisition->toArray(true);
        });

        return response()->json(['requisition' => $requisitionArray, 'userAuth' => $request->get('userAuth')]);
    }

    public function show(string $id, Request $request)
    {
        $requisition = HeadRequ::ApprovedAndAssigned()
            ->withRelations()
            ->withDetailRequisition()
            ->where('RequisicionId', $id)
            ->with(['requDetail' => function ($query) {
                    $query->with(['product' => function ($query) {
                        $query->withSuggestedLocation();
                    }]);
                }])
            ->orderBy('Prioridad', 'asc')
            ->firstOrFail();
        $requisitionArray = $requisition->toArray(true);
        return response()->json(['requisitionData' => $requisitionArray]);
    }
}
