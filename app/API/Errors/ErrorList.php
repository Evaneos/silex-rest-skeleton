<?php

namespace Evaneos\REST\API\Errors;

use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ErrorList
{
    /**
     * @var FieldError[]
     */
    private $errors = array();

    /**
     * ErrorList constructor.
     *
     * @param ConstraintViolationListInterface $violations
     */
    public function __construct(ConstraintViolationListInterface $violations)
    {
        foreach ($violations as $violation) {
            $this->errors[] = new FieldError($violation);
        }
    }
}
