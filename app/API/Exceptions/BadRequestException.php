<?php

namespace Evaneos\REST\API\Exceptions;

class BadRequestException extends \Exception
{
    /**
     * @var string
     */
    private $errors;

    /**
     * Constructor.
     *
     * @param string $errors
     */
    public function __construct($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return string
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
