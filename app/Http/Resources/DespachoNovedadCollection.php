<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DespachoNovedadCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id'    => $this->NovedadId,
            'name'  => $this->Nombre,
            'state' => $this->Estado,
            /* 'detail' => DespachoLogNovedadCollection::collection($this->whenLoaded('posts')), */
        ];
    }
}
