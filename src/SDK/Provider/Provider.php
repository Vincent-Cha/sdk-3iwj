<?php

namespace SDK\Provider;

abstract class Provider{
    abstract public function getClientId();
    abstract public function setClientId($clientId);

    abstract public function getSecret();
    abstract public function setSecret($secret);

    abstract public function getUrl();
    abstract public function getParams();

    protected $state;
    protected $session = null;

    public function __construct()
	{
        $provider = explode("\\",get_called_class());
        $provider = explode('php',$provider[2]);
        $this->setState($provider[0].".123");
        $_SESSION['state'] = $this->getstate();
	}

    protected function getstate()
    {
        return $this->state;
    }

    protected function setState($state)
    {
        $this->state = $state;
    }
    public function getTokenAndUser($params, $settings)
    {
        $queryParams = http_build_query(array_merge([
            'client_id'=> $settings['client_id'],
            'client_secret'=> $settings['client_secret'],
        ], $params));
        $url = $settings['token_url'] . '?' . $queryParams;
        $context = stream_context_create([
            "http"=> [
                "method" => $settings['method'],
                'header'  => [
                    $settings['content-type'],
                    $settings['accept']
                ],
                'content' => $queryParams
            ]
        ]);
        $response = file_get_contents($url,false,$context);
        $response = json_decode($response, true);
        var_dump($response);
        $token = $response['access_token'];
    
        $context = stream_context_create([
            "http"=> [
                "header" => [
                    "Authorization: Bearer " . $token,
                    'User-Agent: SDK'
                ],
            ]
                ]);
        $url = $settings['user_url'];
        $response = file_get_contents($url, false, $context);
        var_dump(json_decode($response, true));
    }

}