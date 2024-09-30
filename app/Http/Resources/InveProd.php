<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InveProd extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */


    public function toArray(Request $request): array
    {
        return [
            'productId'     => $this?->productoid,
            'warehouseId'   => $this?->almacenid,
            'inventory'     => $this?->invenactua,
            'id'            => $this?->InveProdId,


        ];

    }
}
