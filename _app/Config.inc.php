<?php
// CONFIGRAÇÕES DO BANCO EM AMBIENTE DE DESENVOLVIMENTO ####################
define('HOST', 'localhost');
define('USER', 'root');
define('PASS', '');
define('DBSA', 'u264360683_mcr');
/*
// CONFIGRAÇÕES DO BANCO EM AMBIENTE DE PRODUÇÂO ####################
define('HOST', 'j1r4n2ztuwm0bhh5.cbetxkdyhwsb.us-east-1.rds.amazonaws.com');
define('USER', 'i3jpb3r4ggb8922a');
define('PASS', 'x4cwuuwhzngy4okz');
define('DBSA', 'sod4a2777opp9d11');*/

//CONSTANTES DE STATUS HTTP

############## Respostas de sucesso ##############
define('STATUS_OK', 200); //requisição foi bem sucessida
define('STATUS_OK_CREATED', 201); //requisição foi bem sucessida e um novo recurso foi criado como resultado
define('STATUS_OK_ACCEPTED', 202); //requisição foi recebida mas nenhuma ação foi tomada sobre ela
define('STATUS_OK_NO_CONTENT', 204); //não há conteúdo para enviar para esta solicitação, mas os cabeçalhos podem ser úteis.

############## Redirection messages ##############
define('STATUS_MULTIPLE_CHOICE', 300); //a solicitação tem mais de uma resposta possível.
define('STATUS_MOVED_PERMANENTLY', 301); //esse código de resposta significa que o URI do recurso solicitado foi alterado.
define('STATUS_FOUND', 302); //este código de resposta significa que o URI do recurso solicitado foi alterado temporariamente

############## Client error responses ##############
define('STATUS_BAD_REQUEST', 400); //essa resposta significa que o servidor não pôde entender a solicitação devido à sintaxe inválida.
define('STATUS_UNAUTHORIZED', 401); //a autenticação é necessária para obter a resposta solicitada, não autorizado.
//define('STATUS_PAYMENT_REQUIRED', 402); //
define('STATUS_FORBIDDEN', 403); //o cliente não tem direitos de acesso ao conteúdo, portanto, o servidor está rejeitando para dar uma resposta adequada
define('STATUS_NOT_FOUND', 404); //o servidor não pode encontrar o recurso solicitado
define('STATUS_NOT_ACCEPTABLE', 406);

############# SERVER ERROR RESPONSES ####################
define('STATUS_INTERNAL_SERVER_ERROR', 500); //o servidor encontrou uma situação que não sabe como manipular.
define('STATUS_NOT_IMPLEMENTED', 501); //o método de solicitação não é suportado pelo servidor e não pode ser tratado.
define('STATUS_BAD_GATEWAY', 502); //essa resposta de erro significa que o servidor, enquanto trabalha como gateway para obter uma resposta necessária para manipular a solicitação, obteve uma resposta inválida.
define('STATUS_SERVICE_UNAVAILABLE', 503); //o servidor não está pronto para manipular a solicitação.
define('STATUS_GATEWAY_TIMEOUT', 504); //essa resposta de erro é fornecida quando o servidor está agindo como um gateway e não pode obter uma resposta no tempo
define('STATUS_HHTP_VERSION_NOT_SUPPORTED', 505); //a versão HTTP usada na solicitação não é suportada pelo servidor.

############# CONSTANTES DE ERRORS ######################
define('ARRAY_ERRORS',
['Essa resposta significa que o servidor não pôde entender a solicitação devido à sintaxe inválida.',
'A autenticação é necessária para obter a resposta solicitada, não autorizado.',
'O servidor não pode encontrar o recurso solicitado.',
'Essa resposta significa que o servidor não pôde entender a solicitação devido à sintaxe inválida.',
'O servidor encontrou uma situação que não sabe como manipular.',
'O método de solicitação não é suportado pelo servidor e não pode ser tratado.',
'O servidor não está pronto para manipular a solicitação.',
'A versão HTTP usada na solicitação não é suportada pelo servidor.'
]);

// AUTO LOAD DE CLASSES ####################
function __autoload($Class) {

    $cDir = ['Conn', 'Helpers', 'Models'];
    $iDir = null;

    foreach ($cDir as $dirName):
        if (!$iDir && file_exists(__DIR__ . DIRECTORY_SEPARATOR . $dirName . DIRECTORY_SEPARATOR . $Class . '.class.php') && !is_dir(__DIR__ . DIRECTORY_SEPARATOR . $dirName . DIRECTORY_SEPARATOR . $Class . '.class.php')):
            include_once (__DIR__ . DIRECTORY_SEPARATOR . $dirName . DIRECTORY_SEPARATOR . $Class . '.class.php');
            $iDir = true;
        endif;
    endforeach;

    if (!$iDir):
        trigger_error("Não foi possível incluir {$Class}.class.php", E_USER_ERROR);
        die;
    endif;
}

//WSErro :: Exibe erros lançados :: Front
function WSErro($ErrMsg, $ErrNo, $ErrDie = null) {
    $CssClass = ($ErrNo == E_USER_NOTICE ? WS_INFOR : ($ErrNo == E_USER_WARNING ? WS_ALERT : ($ErrNo == E_USER_ERROR ? WS_ERROR : $ErrNo)));
    echo "<p class=\"trigger {$CssClass}\">{$ErrMsg}<span class=\"ajax_close\"></span></p>";

    if ($ErrDie):
        die;
    endif;
}

//PHPErro :: personaliza o gatilho do PHP
function PHPErro($ErrNo, $ErrMsg, $ErrFile, $ErrLine) {
    $CssClass = ($ErrNo == E_USER_NOTICE ? WS_INFOR : ($ErrNo == E_USER_WARNING ? WS_ALERT : ($ErrNo == E_USER_ERROR ? WS_ERROR : $ErrNo)));
    echo "<p class=\"trigger {$CssClass}\">";
    echo "<b>Erro na Linha: #{$ErrLine} ::</b> {$ErrMsg}<br>";
    echo "<small>{$ErrFile}</small>";
    echo "<span class=\"ajax_close\"></span></p>";

    if ($ErrNo == E_USER_ERROR):
        die;
    endif;
}

set_error_handler('PHPErro');
