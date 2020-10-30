<?php

namespace App\Http\Resources\Api\v1;

use App\Traits\ApiResponseMetaData;
use Illuminate\Http\Resources\Json\JsonResource;

class Property extends JsonResource
{
    use ApiResponseMetaData;

    public function toArray($request)
    {
        $word = ($this->is_assoc(parent::toArray($request))) ? 'property' : 'properties';
        return [
            'success' => $this->success,
            'code' => $this->code,
            'message' => $this->message,
            'data' => [
                "{$word}" => parent::toArray($request)
            ]
        ];
    }
}
