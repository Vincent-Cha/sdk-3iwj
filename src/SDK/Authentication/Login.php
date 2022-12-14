<?php

namespace SDK\Authentication;
use Token;

class Login
{
   private Token $token;

    public function __construct($clientId,$secret,$grantType,$settings)
    {
        $this->token = new Token($clientId,$secret,$grantType);
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken(Token $token)
    {
        $this->token = $token;
    }
}