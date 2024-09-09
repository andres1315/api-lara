<?php

namespace App\Http\Controllers;

use App\Models\DespachoNovedad;
use Illuminate\Http\Request;

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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $collection =
            DespachoNovedad::ActiveAndFilterType('AL')
            ->withDetailDispatch()
            ->where('DespachoLogNovedad.DespachoLogId', $id)
            ->get();


        return response()->json(['newsDispatch' => $collection]);

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
