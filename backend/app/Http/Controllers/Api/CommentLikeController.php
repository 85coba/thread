<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Api;

use App\Action\Comment\LikeCommentAction;
use App\Action\Comment\LikeCommentRequest;
use App\Action\Comment\GetLikedUsersIdsByCommentIdAction;
use App\Action\Comment\GetLikedUsersIdsByCommentIdRequest;
use App\Action\User\GetUserCollectionByArrIdAction;
use App\Http\Controllers\ApiController;
use App\Http\Response\ApiResponse;
use App\Action\GetCollectionRequest;
use App\Http\Request\Api\CollectionHttpRequest;
use App\Http\Presenter\UserArrayPresenter;

final class CommentLikeController extends ApiController
{
    private $likeCommentAction;
    private $getLikedUsersIdsByCommentIdAction;
    private $getUserCollectionByArrIdAction;
    private $presenter;

    public function __construct(
        LikeCommentAction $likeCommentAction,
        GetLikedUsersIdsByCommentIdAction $getLikedUsersIdsByCommentIdAction,
        GetUserCollectionByArrIdAction $getUserCollectionByArrIdAction,
        UserArrayPresenter $presenter
    ) {
        $this->likeCommentAction = $likeCommentAction;
        $this->getLikedUsersIdsByCommentIdAction = $getLikedUsersIdsByCommentIdAction;
        $this->getUserCollectionByArrIdAction = $getUserCollectionByArrIdAction;
        $this->presenter = $presenter;
    }

    public function likeOrDislikeComment(string $id): ApiResponse
    {
        $response = $this->likeCommentAction->execute(new LikeCommentRequest((int)$id));

        return $this->createSuccessResponse(['status' => $response->getStatus()]);
    }

    public function getUsersLikedByCommentId(string $id, CollectionHttpRequest $request): ApiResponse
    {
        $usersIds = $this->getLikedUsersIdsByCommentIdAction->execute(
            new GetLikedUsersIdsByCommentIdRequest((int)$id)
        );

        $response = $this->getUserCollectionByArrIdAction->execute(
            $usersIds,
            new GetCollectionRequest(
                (int)$request->query('page'),
                $request->query('sort'),
                $request->query('direction')
            )
        );

        return $this->createPaginatedResponse($response->getPaginator(), $this->presenter);
    }
}
