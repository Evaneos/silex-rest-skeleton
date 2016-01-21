<?php

namespace spec\Evaneos\REST\Security\JWT;

use Evaneos\REST\Security\JWT\JWTToken;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

class JWTListenerSpec extends ObjectBehavior
{
    function let(AuthenticationManagerInterface $authenticationManager, TokenStorageInterface $tokenStorage)
    {
        $this->beConstructedWith($tokenStorage, $authenticationManager);
    }
    function it_is_initializable()
    {
        $this->shouldHaveType('Evaneos\REST\Security\JWT\JWTListener');
    }
    
    function it_implements_ListenerInterface()
    {
        $this->shouldImplement(ListenerInterface::class);
    }

    function it_doesnt_authenticate_if_Authorization_header_is_not_found(AuthenticationManagerInterface $authenticationManager, GetResponseEvent $event)
    {
        $request = new Request();
        $event->getRequest()->willReturn($request);
        $this->handle($event);

        $authenticationManager->authenticate(Argument::any())->shouldNotBeCalled();
    }

    function it_doesnt_authenticate_if_Authorization_Bearer_token_is_not_found(AuthenticationManagerInterface $authenticationManager, GetResponseEvent $event)
    {
        $request = new Request();
        $request->headers->add([
            'Authorization' => 'Something JWTToken'
        ]);

        $event->getRequest()->willReturn($request);
        $this->handle($event);

        $authenticationManager->authenticate(Argument::any())->shouldNotBeCalled();
    }

    function it_authenticates_if_a_Authorization_Bearer_token_is_found(AuthenticationManagerInterface $authenticationManager, GetResponseEvent $event)
    {
        $request = new Request();
        $request->headers->add([
            'Authorization' => 'Bearer JWTToken'
        ]);
        $event->getRequest()->willReturn($request);
        $this->handle($event);

        $jwtToken = new JWTToken();
        $jwtToken->setToken('JWTToken');

        $authenticationManager->authenticate($jwtToken)->shouldBeCalled();
    }

    function it_stores_the_token_returned_by_AuthenticationManager(AuthenticationManagerInterface $authenticationManager, TokenStorageInterface $tokenStorage, GetResponseEvent $event)
    {
        $request = new Request();
        $request->headers->add([
            'Authorization' => 'Bearer JWTToken'
        ]);
        $event->getRequest()->willReturn($request);

        $jwtToken = new JWTToken();
        $jwtToken->setToken('JWTToken');

        $authToken = new JWTToken();
        $authenticationManager->authenticate($jwtToken)->willReturn($authToken);

        $this->handle($event);

        $tokenStorage->setToken($authToken)->shouldBeCalled();
    }
}
