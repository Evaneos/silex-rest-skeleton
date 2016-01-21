<?php

namespace spec\Evaneos\REST\Security\JWT;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class JWTTokenSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Evaneos\REST\Security\JWT\JWTToken');
    }
    
    function it_implements_TokenInterface()
    {
        $this->shouldImplement(TokenInterface::class);
    }
    
    function it_returns_token_as_credentials()
    {
        $this->setToken('JWTToken');
        $this->getCredentials()->shouldReturn('JWTToken');
        
        $this->setToken('AnotherJWTToken');
        $this->getCredentials()->shouldReturn('AnotherJWTToken');
    }
}
