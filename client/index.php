<?php

session_start();

define("OAUTH_CLIENTID", "id_635913414ff1f4.96204950");
define("OAUTH_CLIENTSECRET", "82eb52781cc74ce5564927d4e7223e07ab28f489");
define("FB_CLIENTID", "880024803168727");
define("FB_CLIENTSECRET", "4c34dc15c8c2a74c70baf0d9e1e1a9fb");
define("GIT_CLIENTID", "Iv1.4efcc1802369e28e");
define("GIT_CLIENTSECRET", "aa78ba77f4a4fe6c9c57d5417647a595056dd504");
define("DC_CLIENTID", "1051884779085234228");
define("DC_CLIENTSECRET", "jg1LYzKQFgKAIPrfAdc8ithNeloE3cGM");

function login()
{
    $_SESSION['state'] = uniqid();

    $queryParams = http_build_query([
        'response_type'=> "code",
        'state' => $_SESSION['state'],
        'scope' => 'basic',
        'client_id'=> OAUTH_CLIENTID,
        "redirect_uri"=> "http://localhost:8081/success"
    ]);
    $url = "http://localhost:8080/auth?" . $queryParams;
    echo "Se connecter via OAuthServer (form)";
    echo '<form method="POST" action="do_login">
        <input type="text" name="username"><input type="text" name="password">
        <input type="submit" value="login">
        </form>';
    echo "<a href='$url'>Se connecter via OAuthServer</a>";
    echo "<br>";

    $queryParams = http_build_query([
        'response_type'=> "code",
        'state' => $_SESSION['state'],
        'scope' => '',
        'client_id'=> FB_CLIENTID,
        "redirect_uri"=> "http://localhost:8081/fb_success"
    ]);
    $url = "https://www.facebook.com/v15.0/dialog/oauth?" . $queryParams;
    echo "<a href='$url'>Se connecter via Facebook</a>";
    echo "<br>";

    $queryParams = http_build_query([
        'reponse_type'=> "code",
        'state' => $_SESSION['state'],
        'scope' => 'repo,gist',
        'client_id'=> GIT_CLIENTID,
        "redirect_uri"=> "http://localhost:8081/git_success"
    ]);
    $url = "https://github.com/login/oauth/authorize?" . $queryParams;
    echo "<a href='$url'>Se connecter via GitHub</a>";
    echo "<br>";

    $queryParams = http_build_query([
        'response_type'=> "code",
        'state' => $_SESSION['state'],
        'scope' => 'identify connections',
        'client_id'=> DC_CLIENTID,
        "redirect_uri"=> "http://localhost:8081/dc_success"
    ]);
    $url = "https://discord.com/oauth2/authorize?" . $queryParams;
    echo "<a href='$url'>Se connecter via Discord</a>";
}

function redirectSuccess()
{
    ["code" => $code, "state" => $state] = $_GET;
    if ($state !== $_SESSION['state']) {
        return http_response_code(400);
    }

    getTokenAndUser(
        [
        'grant_type'=> "authorization_code",
        "code" => $code,
        "redirect_uri"=> "http://localhost:8081/success"
    ],
        [
            "client_id" => OAUTH_CLIENTID,
            "client_secret" => OAUTH_CLIENTSECRET,
            "token_url" => "http://server:8080/token",
            "user_url" => "http://server:8080/me",
            "content-type" => "Content-Type: null"
        ]
    );
}

function redirectFbSuccess()
{
    ["code" => $code, "state" => $state] = $_GET;
    if ($state !== $_SESSION['state']) {
        return http_response_code(400);
    }

    getTokenAndUser([
        'grant_type'=> "authorization_code",
        "code" => $code,
        "redirect_uri"=> "http://localhost:8081/fb_success"
    ], [
        "client_id" => FB_CLIENTID,
        "client_secret" => FB_CLIENTSECRET,
        "token_url" => "https://graph.facebook.com/oauth/access_token",
        "user_url" => "https://graph.facebook.com/me",
        "content-type" => "Content-Type: null"
    ]);
}

function redirectDcSuccess()
{
    ["code" => $code, "state" => $state] = $_GET;
    if ($state !== $_SESSION['state']) {
        return http_response_code(400);
    }

    getTokenAndUser([
        'grant_type'=> "authorization_code",
        "code" => $code,
        "redirect_uri"=> "http://localhost:8081/dc_success"
    ], [
        "client_id" => DC_CLIENTID,
        "client_secret" => DC_CLIENTSECRET,
        "token_url" => "https://discord.com/api/oauth2/token",
        "user_url" => "https://discord.com/api/oauth2/@me",
        "method" => 'POST',
        "content-type" => 'Content-Type: application/x-www-form-urlencoded',
        "accept" => 'Accept: application/json'
    ]);
}
function redirectGitSuccess(){
    ["code" => $code, "state" => $state] = $_GET;
    if ($state !== $_SESSION['state']) {
        return http_response_code(400);
    }

    getTokenAndUser([
        'grant_type'=> "authorization_code",
        "code" => $code,
        "redirect_uri"=> "http://localhost:8081/git_success"
    ], [
        "client_id" => GIT_CLIENTID,
        "client_secret" => GIT_CLIENTSECRET,
        "token_url" => "https://github.com/login/oauth/access_token",
        "user_url" => "https://api.github.com/user",
        "method" => 'POST',
        "content-type" => 'Content-Type: application/xml',
        "accept" => 'Accept: application/json'
    ]);
}
function doLogin()
{
    getTokenAndUser(
        [
        'grant_type'=> "password",
        "username" => $_POST['username'],
        "password"=> $_POST['password']
    ],
        [
            "client_id" => OAUTH_CLIENTID,
            "client_secret" => OAUTH_CLIENTSECRET,
            "token_url" => "http://server:8080/token",
            "user_url" => "http://server:8080/me"
        ]
    );
}

function getTokenAndUser($params, $settings)
{
    $queryParams = http_build_query(array_merge([
        'client_id'=> $settings['client_id'],
        'client_secret'=> $settings['client_secret'],
    ], $params));
    $url = $settings['token_url'] . '?' . $queryParams;
    $context = stream_context_create (array(
        "http"=> [
            "method" => $settings['method'] ?? null,
            'header'  => [
                $settings['content-type'] ?? null,
                $settings['accept'] ?? null
            ],
            'content' => $queryParams
        ]
    ));
    $response = file_get_contents($url, false, $context);
    $response = json_decode($response, true);
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

$url = strtok($_SERVER['REQUEST_URI'], '?');
switch($url) {
    case '/login':
        login();
        break;
    case '/success':
        redirectSuccess();
        break;
    case '/do_login':
        doLogin();
        break;
    case '/fb_success':
        redirectFbSuccess();
        break;
    case '/git_success':
        redirectGitSuccess();
        break;
    case '/dc_success':
        redirectDcSuccess();
        break;
    default:
        http_response_code(404);
        break;
}
