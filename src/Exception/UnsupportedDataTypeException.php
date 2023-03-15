<?php

namespace CompressJson\Exception;

use Exception;

class UnsupportedDataTypeException extends Exception
{
    public function __construct($data)
    {
        $type = gettype($data);
        parent::__construct("[$type] is unsupported data type");
    }
}
