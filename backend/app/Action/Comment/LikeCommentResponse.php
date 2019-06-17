<?php

declare(strict_types=1);

namespace App\Action\Comment;

final class LikeCommentResponse
{
    private $status;

    public function __construct(string $status)
    {
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
