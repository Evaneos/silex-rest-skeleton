<?php

namespace Evaneos\REST\Security\JWT;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class JWTAuthenticationProvider implements AuthenticationProviderInterface
{
    /**
     * @var UserConverter
     */
    private $userConverter;

    /**
     * @var JWTDecoder
     */
    private $JWTDecoder;

    /**
     * Constructor.
     *
     * @param UserConverter $userProvider
     * @param JWTDecoder    $JWTDecoder
     */
    public function __construct(UserConverter $userProvider, JWTDecoder $JWTDecoder)
    {
        $this->userConverter = $userProvider;
        $this->JWTDecoder = $JWTDecoder;
    }

    /**
     * Attempts to authenticate a TokenInterface object.
     *
     * @param TokenInterface $token The TokenInterface instance to authenticate
     *
     * @throws AuthenticationException if the authentication fails
     * @return TokenInterface An authenticated TokenInterface instance, never null
     *
     */
    public function authenticate(TokenInterface $token)
    {
        if (!$token instanceof JWTToken) {
            throw new AuthenticationException(sprintf('%s works only for JWTToken', __CLASS__));
        }

        if (!$token->getCredentials()) {
            throw new AuthenticationException('JWTToken must contain a token in order to authenticate.');
        }

        $decodedToken = $this->JWTDecoder->decode($token->getCredentials());

        $user = $this->userConverter->buildUserFromToken($decodedToken);

        $token->setUser($user);

        return $token;
    }

    /**
     * Checks whether this provider supports the given token.
     *
     * @param TokenInterface $token A TokenInterface instance
     *
     * @return bool true if the implementation supports the Token, false otherwise
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof JWTToken;
    }
}
