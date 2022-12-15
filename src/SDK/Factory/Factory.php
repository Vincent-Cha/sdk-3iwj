<?php

namespace SDK\Factory;

use SDK\Provider\GitHub;
use SDK\Provider\Discord;
use SDK\Provider\Facebook;

class Factory{

    public array $providers = [];
    public array $objectsProvider = [];

    public function __construct()
    {
        $providers = file_get_contents("conf.json");
        $providers = \json_decode($providers);
        foreach ($providers as $provider) {
            if (!empty($provider->client_id)&&!empty($provider->client_secret)) {
                array_push($this->providers,$provider);
            }
        }
        $this->instanceProviders($this->providers);
    }

    public function instanceProviders($providers)
    {
        foreach ($providers as $provider) {
            $class = "SDK\Provider\\".$provider->class;
            $object = new $class($provider->client_id,$provider->client_secret);
            array_push($this->objectsProvider,$object);
        }
    }
    public function getProvider($name)
    {
        ['code'=>$code,'state' => $state] = $_GET;
        $state = explode('.',$state);
        $state = $state[0];
        switch ($state) {
            case 'Facebook':
                $object = $this->getObjectByName($state);
                $object = new Facebook($object[0],$object[1]);
                $object->redirectSuccess();
                break;
            case 'GitHub':
                $object = $this->getObjectByName($state);
                $object = new GitHub($object[0],$object[1]);
                $object->redirectSuccess();
                break;
            case 'Discord':
                $object = $this->getObjectByName($state);
                $object = new Discord($object[0],$object[1]);
                $object->redirectSuccess();
                break;
            
            default:
                http_response_code(404); 
                break;
        }
    }

    public function getProviders()
    {
        return $this->objectsProvider;
    }

    public function getObjectByName($name)
    {
        foreach ($this->objectsProvider as $provider) {
            if(explode("\\",get_class($provider))[2] == $name){
                return [$provider->getClientId(), $provider->getSecret()];
            }
        }
    }
}