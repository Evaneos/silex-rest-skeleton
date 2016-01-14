<?php
namespace Evaneos\REST\Security\JWT;

use Evaneos\REST\Security\User;
use Evaneos\REST\Security\UserId;

class SecurityUserConverter implements UserConverter
{
    /**
     * (non-PHPdoc)
     * @see \Evaneos\REST\Security\JWT\UserConverter::buildUserFromToken()
     */
    public function buildUserFromToken($token)
    {
        return new User(new UserId($token->sub));
    }
}
