<?php

namespace SDK\Provider;

use SDK\Provider\Provider;

class GitHub extends Provider{

        
   
    private string $clientId;
    private string $secret;

    private string $params;

    public function __construct(string $clientId,string $secret)
	{
		parent::__construct();
        $this->setClientId($clientId);
        $this->setSecret($secret);
	}

    public function getClientId(){

        return $this->clientId;
    }
    public function setClientId($clientId){
        $this->clientId = $clientId;
    }

    public function getSecret(){
        return $this->secret;
    }

    public function setSecret($secret){;
        $this->secret = $secret;
    }

    public function getParams()
    {
        return $this->params = http_build_query([
            'response_type'=> "code",
            'state' => $_SESSION['state'],
            'scope' => '',
            'client_id'=> $this->getClientId(),
            "redirect_uri"=> "http://localhost:8082/git_success"
        ]);
    }

    public function getUrl(){
        return "https://github.com/login/oauth/authorize?" . $this->getParams(); 
    }

    public function redirectSuccess()
    {
        ["code" => $code, "state" => $state] = $_GET;
        if ($state !== $_SESSION['state']) {
            return http_response_code(400);
        }

        $this->getTokenAndUser([
            'grant_type'=> "authorization_code",
            "code" => $code,
            "redirect_uri"=> "http://localhost:8082/git_success"
        ], [
            "client_id" => $this->getClientId(),
            "client_secret" => $this->getSecret(),
            "token_url" => "https://github.com/login/oauth/access_token",
            "user_url" => "https://api.github.com/user",
            "method" => 'POST',
            "content-type" => 'Content-Type: application/xml',
            "accept" => 'Accept: application/json'
        ]);

    }
}