<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Comment;
use App\Events\NewComment;
use Illuminate\Http\Request;
use App\Jobs\Api\v1\CreateComment;
use App\Jobs\Api\v1\ProcessImages;
use App\Jobs\Api\v1\DeleteComment;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\Api\v1\Comment as CommentResource;

class CommentController extends Controller
{
    public function create(CommentRequest $request)
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

        $comment = $this->dispatchNow(CreateComment::fromRequest($request, auth()->user()));

        $images = $this->dispatchNow(ProcessImages::fromRequest($request, auth()->user(), null, $comment));

        $comment = Comment::find($comment->id);

        broadcast(new NewComment($comment))->toOthers();

        return (new CommentResource($comment))
                    ->success(true)
                    ->code('COMMENT_CREATED')
                    ->message('Comment created successfully.')
                    ->response()
                    ->setStatusCode(201);
    }

    public function destroy(Comment $comment)
    {
        DeleteComment::dispatchNow($comment);

        return (new CommentResource($comment))
                    ->success(true)
                    ->code('COMMENT_DELETED')
                    ->message('Comment deleted successfully.')
                    ->response()
                    ->setStatusCode(204);
    }
}
