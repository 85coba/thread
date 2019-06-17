<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Api;

use App\Action\Tweet\LikeTweetAction;
use App\Action\Tweet\LikeTweetRequest;
use App\Http\Controllers\ApiController;
use App\Http\Response\ApiResponse;
use App\Action\Tweet\GetLikedUsersIdsByTweetIdAction;
use App\Action\Tweet\GetLikedUsersIdsByTweetIdRequest;
use App\Action\GetCollectionRequest;
use App\Action\User\GetUserCollectionByArrIdAction;
use App\Http\Request\Api\CollectionHttpRequest;
use App\Http\Presenter\UserArrayPresenter;

final class LikeController extends ApiController
{
    private $likeTweetAction;
    private $getLikedUsersIdsByTweetIdAction;
    private $getUserCollectionByArrIdAction;
    private $presenter;

    public function __construct(
        LikeTweetAction $likeTweetAction,
        GetLikedUsersIdsByTweetIdAction $getLikedUsersIdsByTweetIdAction,
        GetUserCollectionByArrIdAction $getUserCollectionByArrIdAction,
        UserArrayPresenter $presenter
    ) {
        $this->likeTweetAction = $likeTweetAction;
        $this->getLikedUsersIdsByTweetIdAction = $getLikedUsersIdsByTweetIdAction;
        $this->getUserCollectionByArrIdAction = $getUserCollectionByArrIdAction;
        $this->presenter = $presenter;
    }

    public function likeOrDislikeTweet(string $id): ApiResponse
    {
        $response = $this->likeTweetAction->execute(new LikeTweetRequest((int)$id));

        return $this->createSuccessResponse(['status' => $response->getStatus()]);
    }

    public function getUsersLikedByTweetId(string $id, CollectionHttpRequest $request): ApiResponse
    {
        $usersIds = $this->getLikedUsersIdsByTweetIdAction->execute(
            new GetLikedUsersIdsByTweetIdRequest((int)$id)
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
