<?php

namespace App\Domain\Enums;

enum StatusEnum: string
{
    case DONE = 'done';
    case ERROR = 'error';
}
