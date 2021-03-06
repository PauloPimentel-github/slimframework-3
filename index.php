<?php

// error reporting (this is a demo, after all!)
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once('oauth2/src/OAuth2/Autoloader.php');
OAuth2\Autoloader::register();

// $dsn = 'mysql:dbname=oauth2;host=localhost';
// $username = 'root';
// $password = '';

// $dsn = 'mysql:dbname=pine2z3q1qdak8sh;host=jsftj8ez0cevjz8v.cbetxkdyhwsb.us-east-1.rds.amazonaws.com';
// $username = 'vnr661jvepjrm43m';
// $password = 'ls0fwsqzj328kom1';

// $storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));
// $server = new OAuth2\Server($storage);

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once '_app/Config.inc.php';
require_once '_app/Conn/Conn.class.php';
require_once '_app/Conn/Read.class.php';
require_once '_app/Conn/Create.class.php';
require 'vendor/autoload.php';


/**
* Application Instance
*/
$app = new \Slim\App();

/**
* Path para requisição de um token válido
*/
$app->post('/oauth2/token', function (Request $request, Response $response, array $args) use($app) {
    // Create the keypair
    //$publicKey = file_get_contents("oauth2/keys/pubkey.pem");
    //$privateKey = file_get_contents("oauth2/keys/privkey.pem");
    $publicKey = '';
    $privateKey = '';
    $read = new Read;
    $read->exeRead('oauth_public_keys', "WHERE client_id = :cli_id", "cli_id=ClientID_One");

    $data = ($read->getResult()[0] == null ? null : $read->getResult()[0]);
    //client_id
    $clientId = $data['client_id'];
    //client client_secret
    $clientSecret = $data['client_secret'];
    //public key
    $publicKey = $data['public_key'];
    //private key
    $privateKey = $data['private_key'];

    $supportedScopes = array(
      'basic',
      'admin',
      'super'
    );

    // create storage in memory
    $storage = new OAuth2\Storage\Memory(array(
        'default_scope' => $supportedScopes,
        'keys' => array(
            'public_key'  => $publicKey,
            'private_key' => $privateKey,
            'encryption_algorithm'  => 'HS256', // "RS256" is the default
        ),
        //add a Client for testing
        'client_credentials' => array(
            $clientId => array('client_secret' => $clientSecret),
        )
    ));

    // config storage in server
    $server = new OAuth2\Server($storage, array(
        'use_jwt_access_tokens' => true,
    ));

    // config credential in server
    $server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage)); // minimum config

    // send the response;
    return $response->withHeader('Content-type', 'application/json')
    ->withJson($server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send(), STATUS_OK);
});

/**
* Path para validação de um token
*/
$app->get('/oauth2/teste-token', function (Request $request, Response $response, array $args) use($app) {

    /* for a Resource Server (minimum config) */
    $publicKey = '';
    $read = new Read;
    $read->exeRead('oauth_public_keys', "WHERE client_id = :cli_id", "cli_id=ClientID_One");

    $data = ($read->getResult()[0] == null ? null : $read->getResult()[0]);
    $publicKey = $data['public_key'];

    // no private key necessary
    $keyStorage = new OAuth2\Storage\Memory(array('keys' => array(
        'public_key'  => $publicKey,
    )));

    $server = new OAuth2\Server($keyStorage, array(
        'use_jwt_access_tokens' => true,
    ));

    // verify the JWT Access Token in the request
    if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
        $json = array(
            'status' => 0,
            'response' => 'A autenticação é necessária para obter a resposta solicitada',
            'data' => []
        );

        return $response->withHeader('Content-type', 'application/json')->withJson($json, STATUS_UNAUTHORIZED);
        exit("Failed");

    } else {
        $json = array(
            'status' => 1,
            'response' => 'Autenticado, token válido!!!',
            'data' => []
        );

        return $response->withHeader('Content-type', 'application/json')->withJson($json, STATUS_OK);
    }
});

$app->post('/oauth2/token-keys', function (Request $request, Response $response, array $args) use($app) {

});

/**
* <b>get</b>: Método responsável por resgatar informações das categorias e do site
**/
$app->get('/data-site', function (Request $request, Response $response, array $args) use ($app) {
    //faz uma instância do objeto de leitura no banco
    $readCat = new Read;
    $readSite = new Read;
    //executa o método de leitura na tabela de site
    $readSite->exeRead('site');
    //retorna o dados da tabela site do banco e atribui a variável $dataSite, se não existir dados adiciona null
    $dataSite = ($readSite->getResult()[0] == null ? null : $readSite->getResult()[0]);
    //executa o método de leitura na tabela de categoria
    $readCat->exeRead('categoria');
    //se a leitura tiver sucesso
    if (!$readCat->getResult()) {
        //então adiciona o status 1 informado o sucesso
        $data['status'] = 1;
        $data['response'] = 'Sucesso ao realizar busca, mas no momento não há registro no banco';
        $data['data'] = array();

        return $response->withHeader('Content-type', 'application/json')->withJson($data, STATUS_OK);

    } else {

        $data['status'] = 1;
        $data['response'] = 'Dados retornados com sucesso';
        foreach ($readCat->getResult() as $categoria):
            $data['data']['categorias'][] = $categoria;
            $data['data']['site'] = $dataSite;
        endforeach;

        return $response->withHeader('Content-type', 'application/json')->withJson($data, STATUS_OK);
    }
});

/**
* <b>get</b>: Método responsável por resgatar informações da comunicação REST
* @param $getCep: variável responsável por receber o cep de consulta e implementar a url de serviço
*/
$app->get('/cep={getCep}', function (Request $request, Response $response, array $args) use ($app) {
    //inclui a classe Cep.class.php
    require "_app/Models/Cep.class.php";
    //recupera o valor do cep passado como argumento na url
    $getCep = $args['getCep'];
    //faz a instância do obj cep
    $cep = new Cep($getCep);

    if ($cep->getCallback() == null) {
        $json = array(
            'status' => 0,
            'response' => 'Essa resposta significa que o servidor não pôde entender a solicitação devido à sintaxe inválida',
            'data' => []
        );
        return $response->withHeader('Content-type', 'application/json')->withJson($json, STATUS_OK); //200

    } else {
        $json = array(
            'status' => 1,
            'response' => 'O servidor processou a solicitação com sucesso.',
            'data' => ['cep' => $cep->getCallback()]
        );
        return $response->withHeader('Content-type', 'application/json')->withJson($json, STATUS_NOT_ACCEPTABLE); //406 OK
    }
}
);

$app->get('/cep=', function (Request $request, Response $response, array $args) use ($app) {
    return $response->withHeader('Content-type', 'application/json')->withJson(array('status' => 0,
    'reponse' => 'O servidor não pôde encontrar o recurso solicitado',
    'data' => []), STATUS_NOT_FOUND);
});

$app->get('/categoria={catId}', function (Request $request, Response $response, array $args) use ($app) {
    //faz a instância do objeto de leitura no banco
    $readCatId = new Read;
    //recupera o valor do id passado como argumento
    $catId = $args['catId'];
    //executa a leitura no banco e retorna os dados do id informado
    $readCatId->fullRead("SELECT a.artigo_id as art_id, a.artigo_cat as art_cat, a.artigo_nome as art_nome, a.artigo_foto as art_foto, a.artigo_ativo as art_ativo, a.artigo_contratados as art_contratado FROM categoria c INNER JOIN artigo a ON (c.cat_id = a.artigo_cat) WHERE c.cat_id = {$catId}");

    if ($readCatId->getResult()) {

        $data['status'] = 1;
        $data['response'] = 'O servidor processou a solicitação com sucesso.';
        foreach ($readCatId->getResult() as $categoria) {
            $data['data']['categoria'][] = $categoria;
        }

        return $response->withHeader('Content-type', 'application/json')->withJson($data, STATUS_OK); //200 OK

    } else {

        $data['status'] = 1;
        $data['response'] = 'O servidor processou a solicitação com sucesso, mas não está retornando nenhum conteúdo.';
        $data['data'] = array();

        return $response->withHeader('Content-type', 'application/json')->withJson($data, STATUS_OK_ACCEPTED); // 202 OK aceito mas não retornou nenhum dado
    }
});

/**
* Casdastra um novo usuário no banco
*/
$app->post('/user', function (Request $request, Response $response, array $args) use($app) {
    $getPost = json_decode(file_get_contents("php://input"), true);
    $setPost = array_map('trim', $getPost);
    $post = array_map('strip_tags', $setPost);

    if (isset($getPost)) {
        $create = new Create;
        //array com as informações recebidas via post
        $user = array(
            'user_name' => $post['name'],
            'user_age' => $post['idade'],
            'user_document' => $post['cpf']
        );
        //cadastra o usuário no banco
        $create->exeCreate('users', $user);

        if ($create->getResult()) {
            $json = array('status' => 1, 'response' => 'A requisição foi bem sucessida, usuário cadastrado com sucesso.', 'data' => ['user' => $user]);
            return $response->withJson($json, STATUS_OK);// 200 OK

        } else {
            $json = array('status' => 0,'response' => 'O método de solicitação não é suportado pelo servidor e não pode ser tratado..', 'data' => ['user' => $user]);
            return $response->withJson($json, STATUS_NOT_IMPLEMENTED);
        }
    }
});

$app->run();
