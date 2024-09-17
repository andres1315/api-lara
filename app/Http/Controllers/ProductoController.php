<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function locationByProducto(string $id, string $warehouseid){
        $location = Producto::where("productoid", $id)
            ->withLocationOnWarehouse($warehouseid)
            ->first();
        return response()->json(['location' => $location]);
    }
}
