<?php
require_once __DIR__ . '/vendor/autoload.php';

function getGoogleClient() {
    $client = new Google\Client();
    $client->setClientId("265628163870-d6fvaal9ttcb64o3tdp1infkje88t2u9.apps.googleusercontent.com");
    $client->setClientSecret("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");
    $client->setRedirectUri("http://localhost/website/app/view/frontoffice/google-callback.php");
    $client->addScope("email");
    $client->addScope("profile");
    return $client;
}
?>
