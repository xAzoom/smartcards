<?php

declare(strict_types=1);

namespace App\Exception\WordImporter;

class InvalidRowFormatException extends \Exception
{
    protected $message = 'Invalid row format';
}