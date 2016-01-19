<?php

namespace Evaneos\REST\Security\JWT;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

class JWTListener implements ListenerInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthenticationManagerInterface
     */
    private $authenticationManager;

    /**
     * Constructor.
     *
     * @param TokenStorageInterface          $tokenStorage
     * @param AuthenticationManagerInterface $authenticationManager
     */
    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * This interface must be implemented by firewall listeners.
     *
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (null === $jwtTokenValue = $this->getToken($request->headers->get('Authorization'))) {
            return;
        }

        $jwtToken = new JWTToken();
        $jwtToken->setToken($jwtTokenValue);

        $authToken = $this->authenticationManager->authenticate($jwtToken);

        $this->tokenStorage->setToken($authToken);
    }

    /**
     * @param string $authorizationHeader
     *
     * @return string
     */
    private function getToken($authorizationHeader)
    {
        list($token) = sscanf($authorizationHeader, 'Bearer %s');

        return $token;
    }
}
