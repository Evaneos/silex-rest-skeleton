<?php
namespace Evaneos\REST\Security\JWT;

use Firebase\JWT\JWT;

class JWTDecoder
{
    /**
     * @var String
     */
    private $secretKey;

    /**
     * @var array
     */
    private $allowedAlgorithms;

    /**
     * Constructor
     *
     * @param String $secretKey
     * @param array $allowedAlgorithms
     */
    public function __construct($secretKey, array $allowedAlgorithms = [])
    {
        $this->secretKey = $secretKey;
        $this->allowedAlgorithms = $allowedAlgorithms;
    }

    /**
     * @param String $encodedToken
     * @return object
     */
    public function decode($encodedToken)
    {
        return JWT::decode($encodedToken, $this->secretKey, $this->allowedAlgorithms);
    }
}
