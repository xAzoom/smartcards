<?php

declare(strict_types=1);

namespace App\Exception\WordImporter;

class UnrecognisedWordClassException extends \Exception
{
    protected $message = 'Unrecognised word class';
}