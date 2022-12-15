<?php

namespace SDK\Provider;
use SDK\Provider\Provider;

class Discord extends Provider{

        
    
    private string $clientId;
    private string $secret;

    private string $params;

    public function __construct(string $clientId,string $secret)
	{
		$this->clientId = $clientId;
        $this->secret = $secret;

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

    public function setSecret($secret){
        $this->secret = $secret;
    }

    public function getParams()
    {
        return $this->params = http_build_query([
            'response_type'=> "code",
            'state' => $_SESSION['state'],
            'scope' => 'identify connections',
            'client_id'=> $this->getClientId(),
            "redirect_uri"=> "http://localhost:8082/dc_success"
        ]);
    }

    public function getUrl(){
        return "https://discord.com/oauth2/authorize?" . $this->getParams();
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
            "redirect_uri"=> "http://localhost:8082/dc_success"
        ], [
            "client_id" => $this->getClientId(),
            "client_secret" => $this->getSecret(),
            "token_url" => "https://discord.com/api/oauth2/token",
            "user_url" => "https://discord.com/api/oauth2/@me",
            "method" => 'POST',
            "content-type" => 'Content-Type: application/x-www-form-urlencoded',
            "accept" => 'Accept: application/json'
        ]);
    }
}