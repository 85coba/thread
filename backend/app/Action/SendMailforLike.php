<?php

declare(strict_types=1);

namespace App\Action;

use App\Mail\MailOfLike;
use Illuminate\Support\Facades\Mail;
use App\Repository\UserRepository;

final class SendMailforLike 
{
    private $userRepository;

    public function __construct(UserRepository $userRepository) {

        $this->userRepository = $userRepository;
        
    }

    public function execute ($toUserId, $typeLike, $status)
    {
        $toUser = $this->userRepository->getById($toUserId);
        
        Mail::to($toUser)->send(new MailOfLike($typeLike, $status));
    }
}