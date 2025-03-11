<?php

namespace App\Exceptions;

use Exception;

class FileUploadException extends Exception
{
    public function render($request)
    {
        return response500($this->getMessage(), '');
    }
}
