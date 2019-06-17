<?php

declare(strict_types=1);

namespace App\Action\Comment;

use App\Repository\LikeRepository;

final class GetLikedUsersIdsByCommentIdAction
{
    private $likeRepository;

    public function __construct(LikeRepository $likeRepository)
    {
        $this->likeRepository = $likeRepository;
    }

    public function execute(GetLikedUsersIdsByCommentIdRequest $request)
    {
        
        return $this->likeRepository->getUsersIdByCommentId($request->getCommentId());
    }
}