<?php

namespace App\Http\Resources\Api\v1;

use App\Traits\ApiResponseMetaData;
use Illuminate\Http\Resources\Json\JsonResource;

class Comment extends JsonResource
{
    use ApiResponseMetaData;

    public function toArray($request)
    {
        $word = ($this->is_assoc(parent::toArray($request))) ? 'comment' : 'comments';
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
