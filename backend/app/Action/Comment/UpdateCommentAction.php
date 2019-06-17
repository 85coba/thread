<?php

declare(strict_types=1);

namespace App\Action\Comment;

use App\Exceptions\CommentNotFoundException;
use App\Repository\CommentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

final class UpdateCommentAction
{
    private $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function execute(UpdateCommentRequest $request): UpdateCommentResponse
    {
        try {
            $comment = $this->commentRepository->getById($request->getId());
        } catch (ModelNotFoundException $ex) {
            throw new CommentNotFoundException();
        }

        if ($comment->author_id !== Auth::id()) {
            throw new AuthorizationException();
        }

        $comment->body = $request->getBody() ?: $comment->body;

        $comment = $this->commentRepository->save($comment);

        return new UpdateCommentResponse($comment);
    }
}