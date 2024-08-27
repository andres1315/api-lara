<?php

namespace App\Http\Controllers;

use App\Models\HeadRequ;
use Illuminate\Http\Request;

class RequisicionController extends Controller
{
    //
    public function index(Request $request)
    {
        $users = HeadRequ::where('Estado','!=','NU')->get();
        return response()->json(['users'=>$users,'userAuth'=>$request->get('userAuth')]);
    }
}
