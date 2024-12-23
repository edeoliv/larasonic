<?php

declare(strict_types=1);

namespace App\Enums;

enum AppEnvironment: string
{
    case Local = 'local';
    case Testing = 'testing';
    case Staging = 'staging';
    case Production = 'production';
}
