<?php

namespace App\Http\Controllers;


use App\Http\Resources\DespachoNovedad as DespachoNovedadResource;
use App\Http\Resources\DespachoNovedadCollection;
use App\Models\DespachoLogNovedad;
use App\Models\DespachoNovedad;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class DespachoLogNovedadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dispatchLogId = $request->input('dispatchLogId');
        $dispatchNewsId = $request->input('dispatchNewsId');
        $type = $request->input('type');
        $response = [
            'message' => 'success',
            'status'  => 200,
        ];

        DB::beginTransaction();
        try{
            /**
            * * AL -> ALISTAMIENTO
            * * EM -> EMPAQUE
             */

            $newDispatchLog = DespachoLogNovedad::create([
                'DespachoLogId' => $dispatchLogId,
                'NovedadId' => $dispatchNewsId,
                'Fecha' => date('Y-m-d H:i:s'),
                'Tipo' => $type,
            ]);
            DB::commit();
            $response['data'] =$newDispatchLog;
            return response()->json($response,$response['status']);
        }catch(Throwable $th){
            DB::rollback();
            $response['message'] =  $th->getMessage();
            $response['status'] = 400;
            return response()->json($response,$response['status']);
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       $newsDispatch= new DespachoNovedadCollection(
            DespachoNovedad::ActiveAndFilterType('AL')
            ->withDetailDispatch($id)
            ->where('DespachoLogNovedad.DespachoLogId', $id)
            ->get()
        );

        $allHeadNewsDispatch =new DespachoNovedadCollection(
            DespachoNovedad::where('Estado', 'A')->get()
        );


        return response()->json(['newsDispatch' => $newsDispatch, 'headDispatch' =>$allHeadNewsDispatch]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
