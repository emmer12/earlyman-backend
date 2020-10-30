<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PropertyCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        dd(get_object_vars ($this));
        $collections = parent::toArray($request);
        $new_collection = [];
        foreach($collections as $collection) {
            $collection = $this->array_except($collection, ['success', 'code', 'message']);
            array_push($new_collection, array_values($collection));
        }

        return [
            'success' => 'success',
            'code' => 'PROPERTIES',
            'message' => 'View properties collection',
            'data' => $new_collection,
            'links' => [
                'first' => $this->firstPage()
            ]
        ];
    }

    public function withResponse($request, $response)
    {
        $jsonResponse = json_decode($response->getContent(), true);
        unset($jsonResponse['links'],$jsonResponse['meta']);
        $response->setContent(json_encode($jsonResponse));
    }

    protected function array_except($array, $keys) {
       return array_diff_key($array, array_flip((array) $keys));   
    }
}
