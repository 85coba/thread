<?php

declare(strict_types=1);

namespace App\Action\Comment;

use App\Entity\Like;
use App\Repository\LikeRepository;
use App\Repository\CommentRepository;
use Illuminate\Support\Facades\Auth;
use App\Action\SendMailforLike;


final class LikeCommentAction
{
    private $commentRepository;
    private $likeRepository;
    private $sendMailforLike;
    

    private const LIKE_TYPE = "comment";
    private const ADD_LIKE_STATUS = 'added';
    private const REMOVE_LIKE_STATUS = 'removed';

    public function __construct(CommentRepository $commentRepository, 
                                LikeRepository $likeRepository, 
                                SendMailforLike $sendMailforLike                                
                                )
    {
        $this->commentRepository = $commentRepository;
        $this->likeRepository = $likeRepository;
        $this->sendMailforLike = $sendMailforLike;
    }

    public function execute(LikeCommentRequest $request): LikeCommentResponse
    {
        $comment = $this->commentRepository->getById($request->getCommentId());

        $userId = Auth::id();

        // if user already liked comment, we remove previous like
        if ($this->likeRepository->existsForCommentByUser($comment->id, $userId)) {
            $this->likeRepository->deleteForCommentByUser($comment->id, $userId);

            return new LikeCommentResponse(self::REMOVE_LIKE_STATUS);
        }

        $like = new Like();
        $like->forComment(Auth::id(), $comment->id);

        $this->likeRepository->save($like);

        $this->sendMailforLike->execute($comment->author_id, self::LIKE_TYPE, self::ADD_LIKE_STATUS);

        return new LikeCommentResponse(self::ADD_LIKE_STATUS);
    }
}
