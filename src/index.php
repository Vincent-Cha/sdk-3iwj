<?php

namespace App;

use SDK\Provider\GitHub;
use SDK\Provider\Provider;
use SDK\Factory\Factory;

spl_autoload_register(function ( $class ){

	$filename = str_replace("App\\", "", $class);
	$filename = str_replace("\\", "/", $filename);
	$filename =  $filename.".php";
	if(file_exists($filename)){
		include $filename;
	}



});

define("OAUTH_CLIENTID", "id_635913414ff1f4.96204950");
define("OAUTH_CLIENTSECRET", "82eb52781cc74ce5564927d4e7223e07ab28f489");
define("FB_CLIENTID", "793044745254704");
define("FB_CLIENTSECRET", "29fad9df806d99e7a36aa2ef43d8c65e");
define("GIT_CLIENTID", "cd85ead1ba1f79b26fb7");
define("GIT_CLIENTSECRET", "4936fdb0000fb4c2a3d5a8dab008218e109a7005");
define("DC_CLIENTID", "1051884779085234228");
define("DC_CLIENTSECRET", "jg1LYzKQFgKAIPrfAdc8ithNeloE3cGM");

$url = strtok($_SERVER['REQUEST_URI'], '?');
$factory = new Factory();
$providers = $factory->getProviders();
$object = null;
switch($url) {
    case '/login':  
        foreach ($providers as $provider) {
            $urlProvider = $provider->getUrl();
            echo "<a href='$urlProvider'>Se connecter via ".explode("\\",\get_class($provider))[2]."</a>";
            echo "<br>";
        }
        break;
    case '/fb_success':
        $factory->getProvider('Facebook');
        break;
    case '/git_success';
        $factory->getProvider('GitHub');
        break;
    case '/dc_success';
        $factory->getProvider('Discord');
        break;
    default:
        http_response_code(404);
        break;
}