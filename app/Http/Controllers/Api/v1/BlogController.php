<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Blog;
use Illuminate\Http\Request;
use App\Jobs\Api\v1\DeleteBlog;
use App\Jobs\Api\v1\ProcessBlog;
use App\Http\Requests\BlogRequest;
use App\Jobs\Api\v1\ProcessImages;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\BlogResource;

class BlogController extends Controller
{
    public function index()
    {
        $blog = Blog::latest()->get();
        return (new BlogResource($blog))
                    ->success(true)
                    ->code('BLOG_POSTS')
                    ->message('Blog listing.')
                    ->response()
                    ->setStatusCode(200);
    }

    public function show(Blog $blog)
    {
        return (new BlogResource($blog))
                    ->success(true)
                    ->code('BLOG_DETAIL')
                    ->message('Blog details.')
                    ->response()
                    ->setStatusCode(200);
    }

    public function create(BlogRequest $request)
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

        if (!auth()->user.is_admin) {
            $errors = [];
            return (new ErrorResource(Error::errors($errors)))
                        ->success(false)
                        ->code('UNAUTHORIZED_USER')
                        ->message('You are not authorized to access this route.')
                        ->response()
                        ->setStatusCode(401);
        }

        $blog = $this->dispatchNow(ProcessBlog::fromRequest($request, auth()->user()));

        $images = $this->dispatchNow(ProcessImages::fromRequest($request, auth()->user(), null, null, $blog));

        $blog = Blog::find($blog->id);

        return (new BlogResource($blog))
                    ->success(true)
                    ->code('BLOG_CREATED')
                    ->message('Blog post created successfully.')
                    ->response()
                    ->setStatusCode(201);
    }

    public function update(BlogRequest $request, Blog $blog)
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

        if (!auth()->user.is_admin) {
            $errors = [];
            return (new ErrorResource(Error::errors($errors)))
                        ->success(false)
                        ->code('UNAUTHORIZED_USER')
                        ->message('You are not authorized to access this route.')
                        ->response()
                        ->setStatusCode(401);
        }

        $blog = $this->dispatchNow(ProcessBlog::fromRequest($request, auth()->user(), $blog));

        $images = $this->dispatchNow(ProcessImages::fromRequest($request, auth()->user(), null, null, $blog));

        $blog = Blog::find($blog->id);

        return (new BlogResource($blog))
                    ->success(true)
                    ->code('BLOG_UPDATED')
                    ->message('Blog post updated successfully.')
                    ->response()
                    ->setStatusCode(200);
    }

    public function destroy(Blog $blog)
    {
        if (!auth()->user.is_admin) {
            $errors = [];
            return (new ErrorResource(Error::errors($errors)))
                        ->success(false)
                        ->code('UNAUTHORIZED_USER')
                        ->message('You are not authorized to access this route.')
                        ->response()
                        ->setStatusCode(401);
        }

        DeleteBlog::dispatchNow($blog);

        return (new BlogResource($blog))
                    ->success(true)
                    ->code('BLOG_DELETED')
                    ->message('Blog post deleted successfully.')
                    ->response()
                    ->setStatusCode(204);
    }

    public function search(Request $request)
    {
        $query = ($request->get('q') != '') ? $request->get('q') : '';

        $blog = Blog::where('title', 'sounds like', $query)
                                                ->orWhere('title', 'like', "%{$query}%")
                                                ->orWhere('body', 'like', "%{$query}%")
                                                ->get();

        return (new BlogResource($blog))
                    ->success(true)
                    ->code('BLOG_SEARCH_RESULT')
                    ->message('Search results of blog.')
                    ->response()
                    ->setStatusCode(200);
    }
}
