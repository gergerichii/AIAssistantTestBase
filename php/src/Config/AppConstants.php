<?php

declare(strict_types=1);

namespace App\Config;

readonly class AppConstants
{
    public const string APP_CONFIG_DIR = __DIR__  . DIRECTORY_SEPARATOR;
    public const string APP_TMP_DIR = __DIR__
        . DIRECTORY_SEPARATOR
        . '..'
        . DIRECTORY_SEPARATOR
        . '..'
        . 'tmp'
        . DIRECTORY_SEPARATOR;
}