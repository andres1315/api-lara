<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function locationByProducto(string $id){
        $location = Producto::where("productoid", $id)
            ->withAllLocation()
            ->first();
        return response()->json(['location' => $location]);
    }
}
