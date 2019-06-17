<?php

declare(strict_types = 1);

namespace App\Action\Auth;

use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Auth\Events\PasswordReset;
use Hash;

final class ResetPasswordAction
{
    use ResetsPasswords;

    public function execute($request)
    {
        return $this->reset($request); 
    }

    protected function sendResetLinkResponse($request, $response)
    {
        return response()->json([
            'message' => 'Password reset email sent.',
            'data' => $response
        ]);
    }

    protected function resetPassword($user, $password)
    {
        $user->password = $password;
        $user->save();
        event(new PasswordReset($user));
    }

    protected function sendResetResponse($request, $response)
    {
        return response()->json(['message' => 'Password reset successfully.']);
    }

    protected function sendResetFailedResponse($request, $response)
    {
        return response()->json(['message' => 'Failed, Invalid Token.']);
    }
}