<?php
require_once 'google-api-php-client/src/Google/autoload.php';
require_once("scripts/model/user.php");
session_start();
//require_once 'src/contrib/Google_Oauth2Service.php';

$client = new Google_Client();
$client->setAccessType('online'); // default: offline
$client->setApplicationName('Conctus');
$client->setClientId('81915377937-gotpfkp6his1rrfr6a1vfiqi7q9m0elm.apps.googleusercontent.com');
$client->setClientSecret('RnB-RTwiOhidR4m85LWYmGoO');
$client->setRedirectUri("http://$_SERVER[HTTP_HOST]/login");
//$client->setDeveloperKey('INSERT HERE'); // API key
$client->addScope("https://www.googleapis.com/auth/userinfo.profile");
$client->addScope("https://www.googleapis.com/auth/plus.me");
$client->addScope("email");

$oauth2 = new Google_Service_Oauth2($client);

$authUrl = $client->createAuthUrl();

if (isset($_GET['code'])) { // we received the positive auth callback, get the token and store it in session
    $client->authenticate($_GET['code']);
    $_SESSION['token'] = $client->getAccessToken();
    $token = $_SESSION['token'];
    $_SESSION["userdata"] = $oauth2->userinfo->get();
    $client->setAccessToken($_SESSION['token']);
    $user = \Model\User::GetByGid($_SESSION["userdata"]->id);
    if ($user == null)
    {
        $ud = $_SESSION["userdata"];
        $uid = \Model\User::InsertUser($ud->givenName,$ud->familyName,$ud->email,$ud->picture,$ud->id);
        if ($uid != false)
        {
            $_SESSION["userid"] = $uid;
            header('Location: /welcome');
        }
        else
            header('Location: /error');
    }
    else
    {
        $_SESSION["userid"] = $user->uid;
        header('Location: /');
    }
    die();
}

if (isset($_SESSION['token']) && $_SESSION['token'])
  $client->setAccessToken($_SESSION['token']);

?>