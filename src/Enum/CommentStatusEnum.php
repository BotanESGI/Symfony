<?php

declare(strict_types=1);

namespace App\Enum;

enum CommentStatusEnum: string
{
    case PENDING = 'PENDING';
    case VALIDATED = 'VALIDATED';
    case REJECTED = 'REJECTED';
}
