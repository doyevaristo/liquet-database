<?php

namespace Doyevaristo\LiquetDatabase\Exception;

use Throwable;

class Exception extends \Exception{

    const INVALID_QUERY = 100;
    const ERROR_CONNECTION = 200;
    const FILE_NOT_FOUND = 300;
    const DIRECTORY_NOT_FOUND = 310;
    const IMPORT_ERROR = 400;

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}