<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\TagResource;

class TagController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');
        $tags =  Tag::where('title', 'LIKE', '%' . $query . '%')->get();
        return (new TagResource($tags))
                    ->success(true)
                    ->code('TAGS_SEARCH_RESULT')
                    ->message('Search results of tags.')
                    ->response()
                    ->setStatusCode(200);
    }
}
