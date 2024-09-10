<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DespachoLogNovedad extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->Id,
            'dispatchLogId' => $this->DespachoLogId,
            'newsId'        => $this->NovedadId,
            'date'          => $this->Fecha,
            'type'          => $this->Tipo,

        ];

    }
}
