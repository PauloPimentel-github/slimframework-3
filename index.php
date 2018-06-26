<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once '_app/Config.inc.php';
require_once '_app/Conn/Conn.class.php';
require_once '_app/Conn/Read.class.php';
require_once '_app/Conn/Create.class.php';
require 'vendor/autoload.php';

/* Auth básica Http */
$app->add(new \Slim\Middleware\HttpBasicAuthentication([

    /* Usuários existentes */
    "users" => ["root" => "toor"],

    /* Blacklist - Deixa todas liberadas e só protege as dentro do array */
    "path" => ["/auth"],

    /** Whitelist - Protege todas as rotas e só libera as de dentro do array */
    //"passthrough" => ["/auth/liberada", "/admin/ping"],
]));

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

$app->get('/auth', function (Request $request, Response $response, array $args) use($app) {
    return $response->withJson(["status" => "Autenticado!"], STATUS_OK)->withHeader("Content-type", "application/json");
});

// $app->get('/token', function (Request $request, Response $response) use($app) {
//     require "TokenJWT.php";
//
//     $token = new TokenJWT('Paulo Henrique', 'paulogansobarman@gmail.com', 'key');
//     $received_token = $token->getToken();
//
//     if ($received_token === 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsb2NhbGhvc3QiLCJ1c2VyX25hbWUiOiJQYXVsbyBIZW5yaXF1ZSIsInVzZXJfZW1haWwiOiJwYXVsb2dhbnNvYmFybWFuQGdtYWlsLmNvbSJ9.nVhXFGRQTmH+Ablxpbi3HIvTJqML800eZlu9xed/0o8=') {
//         $jsonJWT = array('status' => 1, 'response' => 'Token válido', 'access_token' => $token->getToken());
//     } else {
//         $jsonJWT = array('status' => 0, 'response' => 'Token inválido suma daqui!', 'access_token' => []);
//     }
//
//     return $response->withHeader("Content-type","application/json")
//                     ->withAddedHeader("Authorization", "Bearer 123")
//                     ->withJson($jsonJWT, STATUS_OK);
// });

$app->run();
