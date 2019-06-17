<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Like;
use App\Entity\Tweet;
use App\Entity\Comment;

final class LikeRepository
{
    public function save(Like $like): Like
    {
        $like->save();

        return $like;
    }

    public function existsForTweetByUser(int $tweetId, int $userId): bool
    {
        return Like::where([
            'likeable_id' => $tweetId,
            'likeable_type' => Tweet::class,
            'user_id' => $userId
        ])->exists();
    }

    public function deleteForTweetByUser(int $tweetId, int $userId): void
    {
        Like::where([
            'likeable_id' => $tweetId,
            'likeable_type' => Tweet::class,
            'user_id' => $userId
        ])->delete();
    }

    public function existsForCommentByUser(int $commentId, int $userId): bool
    {
        return Like::where([
            'likeable_id' => $commentId,
            'likeable_type' => Comment::class,
            'user_id' => $userId
        ])->exists();
    }

    public function deleteForCommentByUser(int $commentId, int $userId): void
    {
        Like::where([
            'likeable_id' => $commentId,
            'likeable_type' => Comment::class,
            'user_id' => $userId
        ])->delete();
    }

    public function getUsersIdByTweetId(int $tweetId) 
    {
        return Like::select('user_id')
            ->where('likeable_id', $tweetId)
            ->where('likeable_type', Tweet::class)
            ->get();
    }

    public function getUsersIdByCommentId(int $commentId) 
    {
        return Like::select('user_id')
            ->where('likeable_id', $commentId)
            ->where('likeable_type', Comment::class)
            ->get();
    }

}
