<?php

namespace App\Http\Controllers;

use App\Models\HeadRequ;
use Illuminate\Http\Request;

class RequisicionController extends Controller
{
    //
    public function index(Request $request)
    {
        $users = HeadRequ::where('Estado','!=','NU')->where('Aprobada','S')->orderBy('Prioridad','asc')->get();
        /*  $users = HeadRequ::Approved()->orderBy('Prioridad','asc')->get(); */
        return response()->json(['requisition'=>$users,'userAuth'=>$request->get('userAuth')]);
    }
}
