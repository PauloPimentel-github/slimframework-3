<?php

/**
 * <b>Create.class:</b>
 * Classe responsável por cadastros genéticos no banco de dados!
 *
 * @copyright (c) 2016, Paulo Pimentel Aluno: UPINSIDE TECNOLOGIA
 */
class Create extends Conn {

    private $tabela;
    private $dados;
    private $result;

    /** @var PDOStatement */
    private $create;

    /** @var PDO */
    private $conn;

    /**
     * <b>ExeCreate:</b> Executa um cadastro simplificado no banco de dados utilizando prepared statements.
     * Basta informar o nome da tabela e um array atribuitivo com nome da coluna e valor!
     *
     * @param STRING $tabela = Informe o nome da tabela no banco!
     * @param ARRAY $dados = Informe um array atribuitivo. ( Nome Da Coluna => Valor ).
     */
    public function exeCreate($tabela, array $dados) {
        $this->tabela = (string) $tabela;
        $this->dados = $dados;

        $this->getSyntax();
        $this->execute();
    }

    /**
     * <b>Obter resultado:</b> Retorna o ID do registro inserido ou FALSE caso nem um registro seja inserido!
     * @return INT $Variavel = lastInsertId OR FALSE
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * ****************************************
     * *********** PRIVATE METHODS ************
     * ****************************************
     */
    //Obtém o PDO e Prepara a query
    private function connect() {
        $this->conn = parent::getConn();
        $this->create = $this->conn->prepare($this->create);
    }

    //Cria a sintaxe da query para Prepared Statements
    private function getSyntax() {
        $fileds = implode(', ', array_keys($this->dados));
        $places = ':' . implode(', :', array_keys($this->dados));
        $this->create = "INSERT INTO {$this->tabela} ({$fileds}) VALUES ({$places})";
    }

    //Obtém a Conexão e a Syntax, executa a query!
    private function execute() {
        $this->connect();
        try {
            $this->create->execute($this->dados);
            $this->result = $this->conn->lastInsertId();
        } catch (PDOException $e) {
            $this->result = null;
            WSErro("<b>Erro ao cadastrar:</b> {$e->getMessage()}", $e->getCode());
        }
    }

}
