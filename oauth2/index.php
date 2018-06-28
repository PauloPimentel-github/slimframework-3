<?php

// error reporting (this is a demo, after all!)
ini_set('display_errors',1);error_reporting(E_ALL);

require_once('src/OAuth2/Autoloader.php');
OAuth2\Autoloader::register();

$dsn = 'mysql:dbname=oauth2;host=localhost';
$username = 'root';
$password = '';

$storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));
$server = new OAuth2\Server($storage);
//$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage)); // or any grant type you like!
//$server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();

// your public key strings can be passed in however you like
$publicKey  = file_get_contents('test/config/keys/id_rsa.pub');
$privateKey = file_get_contents('test/config/keys/id_rsa');

// create storage
$storage = new OAuth2\Storage\Memory(array(
    'keys' => array(
        'public_key'  => $publicKey,
        'private_key' => $privateKey,
    ),
    // add a Client ID for testing
    'client_credentials' => array(
        'CLIENT_ID' => array('client_secret' => 'CLIENT_SECRET')
    ),
));

$server = new OAuth2\Server($storage, array(
    'use_jwt_access_tokens' => true,
));

$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage)); // minimum config

// send the response
$server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
