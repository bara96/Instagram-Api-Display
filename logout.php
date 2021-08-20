<?php
require_once ("InstagramApi.php");

$clientId = "5890618884346526";     //app client id
$clientSecret = "4fc46b8fff6f310d41094d5bdf0ecccc";     //app client secret
$redirectUrl = "https://localhost/ig-api/auth.php";     //redirect url, can also be this page

$igApi = new InstagramApi($clientId, $clientSecret, $redirectUrl);

if($igApi->performInstagramLogout()) {
    $url = 'https://'.$_SERVER['HTTP_HOST'] . "/ig-api";
    header("Location: $url");
    die();
}