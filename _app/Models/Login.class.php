<?php

/**
 * Login.class [ MODEL ]
 * Responável por autenticar, validar, e checar usuário do sistema de login!
 *
 * @copyright (c) 2018, Paulo Pimentel Aluno: UPINSIDE TECNOLOGIA
 */
class Login {

    private $level;
    private $email;
    private $senha;
    private $error;
    private $result;

    /**
     * <b>Informar level:</b> Informe o nível de acesso mínimo para a área a ser protegida.
     * @param INT $level = Nível mínimo para acesso
     */
    function __construct($level) {
        $this->level = (int) $level;
    }

    /**
     * <b>Efetuar Login:</b> Envelope um array atribuitivo com índices STRING user [email], STRING pass.
     * Ao passar este array na ExeLogin() os dados são verificados e o login é feito!
     * @param ARRAY $UserData = user [email], pass
     */
    public function exeLogin(array $userData) {
        $this->email = (string) strip_tags(trim($userData['user']));
        $this->senha = (string) strip_tags(trim($userData['pass']));
        $this->setLogin();
    }

    /**
     * <b>Verificar Login:</b> Executando um getresult é possível verificar se foi ou não efetuado
     * o acesso com os dados.
     * @return BOOL $Var = true para login e false para erro
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * <b>Obter Erro:</b> Retorna um array associativo com uma mensagem e um tipo de erro WS_.
     * @return ARRAY $error = Array associatico com o erro
     */
    public function getError() {
        return $this->error;
    }

    /**
     * <b>Checar Login:</b> Execute esse método para verificar a sessão USERLOGIN e revalidar o acesso
     * para proteger telas restritas.
     * @return BOLEAM $login = Retorna true ou mata a sessão e retorna false!
     */
    public function checkLogin() {
        if (empty($_SESSION['userlogin']) || $_SESSION['userlogin']['user_level'] < $this->level):
            unset($_SESSION['userlogin']);
            return false;
        else:
            return true;
        endif;
    }

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */

    //Valida os dados e armazena os erros caso existam. Executa o login!
    private function setLogin() {
        if (!$this->email || !$this->senha || !Check::email($this->email)):
            $this->error = ['Informe seu E-mail e senha para efetuar o login!', WS_INFOR];
            $this->result = false;
        elseif (!$this->getUser()):
            $this->error = ['Os dados informados não são compatíveis!', WS_ALERT];
            $this->result = false;
        elseif ($this->result['user_level'] < $this->level):
            $this->error = ["Desculpe {$this->result['user_name']}, você não tem permissão para acessar esta área!", WS_error];
            $this->result = false;
        else:
            $this->execute();
        endif;
    }

    //Vetifica usuário e senha no banco de dados!
    private function getUser() {
        $this->senha = md5($this->senha);

        $read = new Read;
        $read->exeRead("users", "WHERE user_email = :e AND user_password = :p", "e={$this->email}&p={$this->senha}");

        if ($read->getResult()):
            $this->result = $read->getResult()[0];
            return true;
        else:
            return false;
        endif;
    }

    //Executa o login armazenando a sessão!
    private function execute() {
        if (!session_id()):
            session_start();
        endif;

        $_SESSION['userlogin'] = $this->result;

        $this->error = ["Olá {$this->result['user_name']}, seja bem vindo(a). Aguarde redirecionamento!", WS_ACCEPT];
        $this->result = true;
    }

}
