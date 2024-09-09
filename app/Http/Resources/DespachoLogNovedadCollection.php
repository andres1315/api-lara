<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DespachoLogNovedadCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
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
