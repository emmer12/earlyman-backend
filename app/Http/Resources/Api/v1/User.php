<?php

namespace App\Http\Resources\Api\v1;

use App\Traits\ApiResponseMetaData;
use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    use ApiResponseMetaData;
    
    public function toArray($request)
    {
        return [
            'success' => $this->success,
            'code' => $this->code,
            'message' => $this->message,
            'data' => [
                "user" => parent::toArray($request)
            ]
        ];
    }
}
