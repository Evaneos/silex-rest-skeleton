<?php

namespace Evaneos\REST\Security\JWT;

use Firebase\JWT\JWT;

class JWTDecoder
{
    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var array
     */
    private $allowedAlgorithms;

    /**
     * Constructor.
     *
     * @param string $secretKey
     * @param array  $allowedAlgorithms
     */
    public function __construct($secretKey, array $allowedAlgorithms = [])
    {
        $this->secretKey = $secretKey;
        $this->allowedAlgorithms = $allowedAlgorithms;
    }

    /**
     * @param string $encodedToken
     *
     * @return object
     */
    public function decode($encodedToken)
    {
        return JWT::decode($encodedToken, $this->secretKey, $this->allowedAlgorithms);
    }
}
