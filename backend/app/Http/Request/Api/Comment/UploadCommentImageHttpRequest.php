<?php

declare(strict_types=1);

namespace App\Http\Request\Api\Comment;

use App\Http\Request\ApiFormRequest;

final class UploadCommentImageHttpRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'image' => 'required|image'
        ];
    }
}
