<?php

declare(strict_types=1);

namespace App\Action\Comment;

use Illuminate\Support\Facades\Auth;
use App\Repository\CommentRepository;

final class GetCommentCollectionByUserIdAction
{
    private $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function execute($userId)
    {
        return $this->commentRepository->getCommentsByUserId($userId);
    }
}
