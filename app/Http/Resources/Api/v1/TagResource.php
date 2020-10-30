<?php

namespace App\Http\Resources\Api\v1;

use App\Traits\ApiResponseMetaData;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    use ApiResponseMetaData;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'success' => $this->success,
            'code' => $this->code,
            'message' => $this->message,
            'data' => [
                "tags" => parent::toArray($request)
                // TODO: add words method and propert to resource traites.
            ]
        ];
    }
}
