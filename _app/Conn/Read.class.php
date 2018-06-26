<?php

/**
 * <b>Read.class:</b>
 * Classe responsável por leituras genéticas no banco de dados!
 *
 * @copyright (c) 2018, Paulo Pimentel Aluno: UPINSIDE TECNOLOGIA
 */
class Read extends Conn {

    private $select;
    private $places;
    private $result;

    /** @var PDOStatement */
    private $read;

    /** @var PDO */
    private $conn;

    /**
     * <b>Exe Read:</b> Executa uma leitura simplificada com Prepared Statments. Basta informar o nome da tabela,
     * os termos da seleção e uma analize em cadeia (ParseString) para executar.
     * @param STRING $Tabela = Nome da tabela
     * @param STRING $Termos = WHERE | ORDER | LIMIT :limit | OFFSET :offset
     * @param STRING $ParseString = link={$link}&link2={$link2}
     */
    public function exeRead($tabela, $termos = null, $parseString = null) {
        if (!empty($parseString)):
            parse_str($parseString, $this->places);
        endif;

        $this->select = "SELECT * FROM {$tabela} {$termos}";
        $this->execute();
    }

    /**
     * <b>Obter resultado:</b> Retorna um array com todos os resultados obtidos. Envelope primário númérico. Para obter
     * um resultado chame o índice getResult()[0]!
     * @return ARRAY $this = Array ResultSet
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * <b>Contar Registros: </b> Retorna o número de registros encontrados pelo select!
     * @return INT $Var = Quantidade de registros encontrados
     */
    public function getRowCount() {
        return $this->read->rowCount();
    }

    public function fullRead($query, $parseString = null) {
        $this->select = (string) $query;
        if (!empty($parseString)):
            parse_str($parseString, $this->places);
        endif;
        $this->execute();
    }

    /**
     * <b>Full Read:</b> Executa leitura de dados via query que deve ser montada manualmente para possibilitar
     * seleção de multiplas tabelas em uma única query!
     * @param STRING $Query = Query Select Syntax
     * @param STRING $ParseString = link={$link}&link2={$link2}
     */
    public function setPlaces($parseString) {
        parse_str($parseString, $this->places);
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
        $this->read = $this->conn->prepare($this->select);
        $this->read->setFetchMode(PDO::FETCH_ASSOC);
    }

    //Cria a sintaxe da query para Prepared Statements
    private function getSyntax() {
        if ($this->places):
            foreach ($this->places as $vinculo => $valor):
                if ($vinculo == 'limit' || $vinculo == 'offset'):
                    $valor = (int) $valor;
                endif;
                $this->read->bindValue(":{$vinculo}", $valor, ( is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR));
            endforeach;
        endif;
    }

    //Obtém a Conexão e a Syntax, executa a query!
    private function execute() {
        $this->connect();
        try {
            $this->getSyntax();
            $this->read->execute();
            $this->result = $this->read->fetchAll();
        } catch (PDOException $e) {
            $this->result = null;
            WSErro("<b>Erro ao Ler:</b> {$e->getMessage()}", $e->getCode());
        }
    }

}
