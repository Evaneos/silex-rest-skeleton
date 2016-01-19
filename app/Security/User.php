<?php

namespace Evaneos\REST\Security;

class User
{
    /**
     * @var UserId
     */
    private $id;

    /**
     * Constructor.
     *
     * @param UserId $id
     */
    public function __construct(UserId $id)
    {
        $this->id = $id;
    }

    /**
     * @return UserId
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->id;
    }
}
