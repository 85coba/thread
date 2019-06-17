<?php

declare(strict_types = 1);

namespace App\Action\Auth;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;


final class SendResetPasswordAction
{
    use SendsPasswordResetEmails;

    public function execute($request)
    {
        return $this->sendResetLinkEmail($request); 
    }

    protected function sendResetLinkResponse($request, $response)
    {
        return response()->json([
            'message' => 'Password reset email sent.',
            'data' => $response
        ]);
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return response()->json(['message' => 'Email could not be sent to this email address.']);
    }
}