<?php

namespace App\Http\Resources;

use App\Http\Resources\DespachoLogNovedad as DespachoLogNovedadResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DespachoNovedad extends JsonResource{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->NovedadId,
            'name' => $this->Nombre,
            'state' => $this->Estado,
            'detail' =>  DespachoLogNovedadResource::collection($this->whenLoaded('detailNewsDispatch')),
        ];
    }
}
