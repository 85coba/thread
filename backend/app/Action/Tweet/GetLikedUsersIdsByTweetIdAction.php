<?php

declare(strict_types=1);

namespace App\Action\Tweet;

use App\Repository\LikeRepository;

final class GetLikedUsersIdsByTweetIdAction
{
    private $likeRepository;

    public function __construct(LikeRepository $likeRepository)
    {
        $this->likeRepository = $likeRepository;
    }

    public function execute(GetLikedUsersIdsByTweetIdRequest $request)
    {
        
        return $this->likeRepository->getUsersIdByTweetId($request->getTweetId());
    }
}