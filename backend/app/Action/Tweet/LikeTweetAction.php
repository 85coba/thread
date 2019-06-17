<?php

declare(strict_types=1);

namespace App\Action\Tweet;

use App\Entity\Like;
use App\Repository\LikeRepository;
use App\Repository\TweetRepository;
use Illuminate\Support\Facades\Auth;
use App\Action\SendMailforLike;

final class LikeTweetAction
{
    private $tweetRepository;
    private $likeRepository;
    private $sendMailforLike;

    private const LIKE_TYPE = "tweet";
    private const ADD_LIKE_STATUS = 'added';
    private const REMOVE_LIKE_STATUS = 'removed';

    public function __construct(
        TweetRepository $tweetRepository, 
        LikeRepository $likeRepository, 
        SendMailforLike $sendMailforLike)
    {
        $this->tweetRepository = $tweetRepository;
        $this->likeRepository = $likeRepository;
        $this->sendMailforLike = $sendMailforLike;
    }

    public function execute(LikeTweetRequest $request): LikeTweetResponse
    {
        $tweet = $this->tweetRepository->getById($request->getTweetId());

        $userId = Auth::id();

        // if user already liked tweet, we remove previous like
        if ($this->likeRepository->existsForTweetByUser($tweet->id, $userId)) {
            $this->likeRepository->deleteForTweetByUser($tweet->id, $userId);

            return new LikeTweetResponse(self::REMOVE_LIKE_STATUS);
        }

        $like = new Like();
        $like->forTweet(Auth::id(), $tweet->id);

        $this->likeRepository->save($like);

        $this->sendMailforLike->execute($tweet->author_id, self::LIKE_TYPE, self::ADD_LIKE_STATUS);

        return new LikeTweetResponse(self::ADD_LIKE_STATUS);
    }
}
