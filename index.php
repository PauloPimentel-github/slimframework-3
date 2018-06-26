<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;

require_once '_app/Config.inc.php';
require_once '_app/Conn/Conn.class.php';
require_once '_app/Conn/Read.class.php';
require_once '_app/Conn/Create.class.php';
require 'vendor/autoload.php';

/**
 * Token do nosso JWT
 */
$container['secretkey'] = "secretloko";

/**
 * Application Instance
 */
$app = new \Slim\App($container);

// /* Auth básica Http */
$app->add(new \Slim\Middleware\HttpBasicAuthentication([

    /* Usuários existentes */
    "users" => ["root" => "toor"],

    /* Blacklist - Deixa todas liberadas e só protege as dentro do array */
    "path" => ["/auth"],

    /** Whitelist - Protege todas as rotas e só libera as de dentro do array */
    //"passthrough" => ["/auth/liberada", "/admin/ping"],
]));

// $app->get('/auth', function (Request $request, Response $response, array $args) use($app) {
//     //return $response->withJson(["status" => "Autenticado!"], STATUS_OK)->withHeader("Content-type", "application/json");
// });


/**
 * HTTP Auth - Autenticação minimalista para retornar um JWT
 */
$app->get('/auth', function (Request $request, Response $response) use ($app) {
    $key = $this->get("secretkey");


    $token = array(
        "user" => "@fidelissauro",
        "twitter" => "https://twitter.com/fidelissauro",
        "github" => "https://github.com/msfidelis"
    );
    $jwt = JWT::encode($token, $key);
    return $response->withJson(["auth-jwt" => $jwt], 200)
        ->withHeader('Content-type', 'application/json');
});

$app->get('/home', function (Request $request, Response $response) use ($app) {

    return $response->withJson(["logado" => 'Token válido, seja bem-vindo'], 200)
        ->withHeader('Content-type', 'application/json');
});

/**
* Auth básica do JWT
* Whitelist - Bloqueia tudo, e só libera os
* itens dentro do "passthrough"
*/
$app->add(new \Slim\Middleware\JwtAuthentication([
    "regexp" => "/(.*)/", //Regex para encontrar o Token nos Headers - Livre
    "header" => "X-Token", //O Header que vai conter o token
    "path" => "/", //Vamos cobrir toda a API a partir do /
    "passthrough" => ["/auth"], //Vamos adicionar a exceção de cobertura a rota /auth
    "realm" => "Protected",
    "secret" => $container['secretkey'] //Nosso secretkey criado
]));

$app->run();
