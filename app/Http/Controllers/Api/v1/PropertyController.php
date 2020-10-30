<?php

namespace App\Http\Controllers\Api\v1;

use App\User;
use App\Models\Property;
use App\Models\Error;
use Illuminate\Http\Request;
use App\Jobs\Api\v1\LikeObjects;
use App\Jobs\Api\v1\UnlikeObjects;
use App\Jobs\Api\v1\CreateProperty;
use App\Jobs\Api\v1\ProcessProperty;
use App\Jobs\Api\v1\DeleteProperty;
use App\Jobs\Api\v1\ProcessImages;
use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyRequest;
use App\Http\Resources\Api\v1\PropertyCollection;
use App\Http\Resources\Api\v1\Error as ErrorResource;
use App\Http\Resources\Api\v1\Property as PropertyResource;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::latest()->get();
        return (new PropertyResource($properties))
                    ->success(true)
                    ->code('PROPERTIES')
                    ->message('Property listing.')
                    ->response()
                    ->setStatusCode(200);
    }

    public function show(Property $property)
    {
        return (new PropertyResource($property))
                    ->success(true)
                    ->code('PROPERTY_DETAIL')
                    ->message('Property details.')
                    ->response()
                    ->setStatusCode(200);
    }

    public function create(PropertyRequest $request)
    {
        if ($request->validator->fails()) {
            $errors = $request->validator->messages();
            return (new ErrorResource(Error::errors($errors)))
                        ->success(false)
                        ->code('INVALID_FIELD')
                        ->message('Invalid fields. Check form and try again.')
                        ->response()
                        ->setStatusCode(400);
        }

        $property = $this->dispatchNow(ProcessProperty::fromRequest($request, auth()->user()));

        $images = $this->dispatchNow(ProcessImages::fromRequest($request, auth()->user(), $property));

        $property = Property::find($property->id);

        return (new PropertyResource($property))
                    ->success(true)
                    ->code('PROPERTY_CREATED')
                    ->message('Property created successfully.')
                    ->response()
                    ->setStatusCode(201);
    }

    public function update(PropertyRequest $request, Property $property)
    {
        if ($request->validator->fails()) {
            $errors = $request->validator->messages();
            return (new ErrorResource(Error::errors($errors)))
                        ->success(false)
                        ->code('INVALID_FIELD')
                        ->message('Invalid fields. Check form and try again.')
                        ->response()
                        ->setStatusCode(400);
        }

        $property = $this->dispatchNow(ProcessProperty::fromRequest($request, auth()->user(), $property));

        $images = $this->dispatchNow(ProcessImages::fromRequest($request, auth()->user(), $property));

        $property = Property::find($property->id);

        return (new PropertyResource($property))
                    ->success(true)
                    ->code('PROPERTY_UPDATED')
                    ->message('Property updated successfully.')
                    ->response()
                    ->setStatusCode(200);
    }

    public function destroy(Property $property)
    {
        DeleteProperty::dispatchNow($property);

        return (new PropertyResource($property))
                    ->success(true)
                    ->code('PROPERTY_DELETED')
                    ->message('Property deleted successfully.')
                    ->response()
                    ->setStatusCode(204);
    }

    public function like(Property $property)
    {
        LikeObjects::dispatchNow($property, auth()->user());

        $property = Property::find($property->id);

        return (new PropertyResource($property))
                    ->success(true)
                    ->code('PROPERTY_LIKED')
                    ->message('Property liked successfully.')
                    ->response()
                    ->setStatusCode(200);
    }

    public function unlike(Property $property)
    {
        UnlikeObjects::dispatchNow($property, auth()->user());

        $property = Property::find($property->id);

        return (new PropertyResource($property))
                    ->success(true)
                    ->code('PROPERTY_UNLIKED')
                    ->message('Property unliked successfully.')
                    ->response()
                    ->setStatusCode(200);
    }

    public function search(Request $request)
    {
        $query = ($request->get('q') != '') ? $request->get('q') : '';

        $properties = Property::with('tags')->where('title', 'sounds like', $query)
                                                ->orWhere('title', 'like', "%{$query}%")
                                                ->orWhere('body', 'like', "%{$query}%")
                                                ->orWhereHas('tags', function($q) use ($query) {
                                                    $q->where('title', 'like', "%{$query}%");
                                                })->get();


        return (new PropertyResource($properties))
                    ->success(true)
                    ->code('PROPERTIES_SEARCH_RESULT')
                    ->message('Search results of properties.')
                    ->response()
                    ->setStatusCode(200);
    }

    public function close(Property $property)
    {
        $property->is_closed = true;
        $property->save();

        return (new PropertyResource($property))
                    ->success(true)
                    ->code('PROPERTY_CLOSED')
                    ->message('Property has been closed successfully.')
                    ->response()
                    ->setStatusCode(200);
    }

    public function open(Property $property)
    {
        $property->is_closed = false;
        $property->save();

        return (new PropertyResource($property))
                    ->success(true)
                    ->code('PPOPERTY_OPEN')
                    ->message('Property has been opened successfully.')
                    ->response()
                    ->setStatusCode(200);
    }

    public function showMyProperties(Request $request)
    {
        if ($request->exists('username')) {
            $user = User::findByUsername($request->get('username'));
        } else if ($request->exists('email')) {
            $user = User::findByEmail($request->get('email'));
        } else {
            $user = auth()->guard('api')->user();
        }

        $property = Property::where('user_id', $user->id)->get();

        return (new PropertyResource($property))
                    ->success(true)
                    ->code('PROFILE_PROPERTY')
                    ->message('View user profile properties.')
                    ->response()
                    ->setStatusCode(200);
    }
}
