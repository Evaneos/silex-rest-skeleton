<?php

namespace spec\Evaneos\REST\Security\JWT;

use Evaneos\REST\Security\JWT\JWTDecoder;
use Evaneos\REST\Security\JWT\JWTToken;
use Evaneos\REST\Security\JWT\UserConverter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class JWTAuthenticationProviderSpec extends ObjectBehavior
{
    function let(UserConverter $userProvider, JWTDecoder $JWTDecoder)
    {
        $this->beConstructedWith($userProvider, $JWTDecoder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Evaneos\REST\Security\JWT\JWTAuthenticationProvider');
    }

    function it_implements_AuthenticationProviderInterface()
    {
        $this->shouldImplement(AuthenticationProviderInterface::class);
    }
    
    function it_supports_JWTToken()
    {
        $this->supports(new JWTToken())->shouldReturn(true);
    }
    
    function it_doesnt_support_a_random_TokenInterface(TokenInterface $token)
    {
        $this->supports($token)->shouldReturn(false);
    }

    function it_throws_an_AuthenticationException_the_JWTToken_doesnt_contain_credentials()
    {
        $jwtToken = new JWTToken();
        $this->shouldThrow(AuthenticationException::class)->during('authenticate', [$jwtToken]);
    }

    function it_throws_an_AuthenticationException_if_passed_token_is_not_a_JWTToken(TokenInterface $token)
    {
        $this->shouldThrow(AuthenticationException::class)->during('authenticate', [$token]);
    }
    
    function it_enriches_the_JWTToken_with_the_user_returned_by_user_convert(UserConverter $userProvider, JWTDecoder $JWTDecoder, JWTToken $jwtToken)
    {
        $jwtToken->getCredentials()->willReturn('JWTToken');

        $JWTDecoder->decode('JWTToken')->willReturn('decodedToken');

        $userProvider->buildUserFromToken('decodedToken')->willReturn('AUser');

        $jwtToken->setUser('AUser')->shouldBeCalled();

        $this->authenticate($jwtToken);
    }
    
    function it_returns_the_provided_JWTToken(UserConverter $userProvider, JWTDecoder $JWTDecoder)
    {
        $jwtToken = new JWTToken();
        $jwtToken->setToken('JWTToken');

        $JWTDecoder->decode('JWTToken')->willReturn('decodedToken');

        $userProvider->buildUserFromToken('decodedToken')->willReturn('AUser');

        $this->authenticate($jwtToken)->shouldReturn($jwtToken);
    }
}
