<?php

namespace Evaneos\REST\API\Errors;

use Symfony\Component\Validator\ConstraintViolationInterface;

final class FieldError
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $field;

    /**
     * Error constructor.
     *
     * @param ConstraintViolationInterface $violation
     */
    public function __construct(ConstraintViolationInterface $violation)
    {
        $this->field = $violation->getPropertyPath();
        $this->message = $violation->getMessage();
    }
}
