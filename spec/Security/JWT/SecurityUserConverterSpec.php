<?php

namespace spec\Evaneos\REST\Security\JWT;

use Evaneos\REST\Security\JWT\UserConverter;
use Evaneos\REST\Security\User;
use Evaneos\REST\Security\UserId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SecurityUserConverterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Evaneos\REST\Security\JWT\SecurityUserConverter');
    }
    
    function it_implements_UserConverter()
    {
        $this->shouldImplement(UserConverter::class);
    }

    function it_builds_a_User()
    {
        $token = new \stdClass();
        $token->sub = 1;
        $token->username = 'Arnaud';

        $this->buildUserFromToken($token)->shouldBeAnInstanceOf(User::class);
    }
    
    function it_builds_a_User_with_correct_id()
    {
        $token = new \stdClass();
        $token->sub = '1234';
        $token->username = 'Joe';
        
        $this->buildUserFromToken($token)->shouldBeLike(new User(new UserId(1234), 'Joe'));
    }

    function it_builds_a_User_with_correct_name()
    {
        $token = new \stdClass();
        $token->sub = '1234';
        $token->username = 'Louis';

        $this->buildUserFromToken($token)->shouldBeLike(new User(new UserId(1234), 'Louis'));
    }
}
