<?php

/**
 * <b>Delete.class:</b>
 * Classe responsável por deletar genéricamente no banco de dados!
 *
 * @copyright (c) 2016, Paulo Pimentel Aluno: UPINSIDE TECNOLOGIA
 */
class Delete extends Conn {

    private $tabela;
    private $termos;
    private $places;
    private $result;

    /** @var PDOStatement */
    private $delete;

    /** @var PDO */
    private $conn;

    public function ExeDelete($tabela, $termos, $parseString) {
        $this->tabela = (string) $tabela;
        $this->termos = (string) $termos;

        parse_str($parseString, $this->places);
        $this->getSyntax();
        $this->execute();
    }

    public function getResult() {
        return $this->result;
    }

    public function getRowCount() {
        return $this->delete->rowCount();
    }

    public function setPlaces($parseString) {
        parse_str($parseString, $this->places);
        $this->getSyntax();
        $this->execute();
    }

    /**
     * ****************************************
     * *********** PRIVATE METHODS ************
     * ****************************************
     */
    //Obtém o PDO e Prepara a query
    private function connect() {
        $this->conn = parent::getConn();
        $this->delete = $this->conn->prepare($this->delete);
    }

    //Cria a sintaxe da query para Prepared Statements
    private function getSyntax() {
        $this->delete = "DELETE FROM {$this->tabela} {$this->termos}";
    }

    //Obtém a Conexão e a Syntax, executa a query!
    private function execute() {
        $this->connect();
        try {
            $this->delete->execute($this->places);
            $this->result = true;
        } catch (PDOException $e) {
            $this->result = null;
            WSErro("<b>Erro ao Deletar:</b> {$e->getMessage()}", $e->getCode());
        }
    }

}
