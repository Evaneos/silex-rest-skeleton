<?php
namespace Evaneos\REST\Security\JWT;

use Evaneos\REST\Security\User;

interface UserConverter
{
    /**
     * @param mixed $token
     * @return User
     */
    public function buildUserFromToken($token);
}
