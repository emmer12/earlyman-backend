<?php

namespace App\Http\Resources\Api\v1;

use App\Traits\ApiResponseMetaData;
use Illuminate\Http\Resources\Json\JsonResource;

class Error extends JsonResource
{
    use ApiResponseMetaData;

    public function toArray($request)
    {
        return [
            'success' => $this->success,
            'code' => $this->code,
            'message' => $this->message,
            "errors" => array_values(parent::toArray($request))
        ];
    }
}
