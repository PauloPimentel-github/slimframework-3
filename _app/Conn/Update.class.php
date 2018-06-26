<?php

/**
 * <b>Update.class:</b>
 * Classe responsável por atualizações genéticas no banco de dados!
 *
 * @copyright (c) 201, Paulo Pimentel Aluno: UPINSIDE TECNOLOGIA
 */
class Update extends Conn {

    private $tabela;
    private $dados;
    private $termos;
    private $places;
    private $result;

    /** @var PDOStatement */
    private $update;

    /** @var PDO */
    private $conn;

    /**
     * <b>Exe Update:</b> Executa uma atualização simplificada com Prepared Statments. Basta informar o
     * nome da tabela, os dados a serem atualizados em um Attay Atribuitivo, as condições e uma
     * analize em cadeia (ParseString) para executar.
     * @param STRING $tabela = Nome da tabela
     * @param ARRAY $dados = [ NomeDaColuna ] => Valor ( Atribuição )
     * @param STRING $termos = WHERE coluna = :link AND.. OR..
     * @param STRING $ParseString = link={$link}&link2={$link2}
     */
    public function exeUpdate($tabela, array $dados, $termos, $parseString) {
        $this->tabela = (string) $tabela;
        $this->dados = $dados;
        $this->termos = (string) $termos;

        parse_str($parseString, $this->places);
        $this->getSyntax();
        $this->execute();
    }

    /**
     * <b>Obter resultado:</b> Retorna TRUE se não ocorrer erros, ou FALSE. Mesmo não alterando os dados se uma query
     * for executada com sucesso o retorno será TRUE. Para verificar alterações execute o getRowCount();
     * @return BOOL $Var = True ou False
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * <b>Contar Registros: </b> Retorna o número de linhas alteradas no banco!
     * @return INT $Var = Quantidade de linhas alteradas
     */
    public function getRowCount() {
        return $this->update->rowCount();
    }

    /**
     * <b>Modificar Links:</b> Método pode ser usado para atualizar com Stored Procedures. Modificando apenas os valores
     * da condição. Use este método para editar múltiplas linhas!
     * @param STRING $ParseString = id={$id}&..
     */
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
        $this->update = $this->conn->prepare($this->update);
    }

    //Cria a sintaxe da query para Prepared Statements
    private function getSyntax() {
        foreach ($this->dados as $key => $value):
            $places[] = $key . ' = :' . $key;
        endforeach;

        $places = implode(', ', $places);
        $this->update = "UPDATE {$this->tabela} SET {$places} {$this->termos}";
    }

    //Obtém a Conexão e a Syntax, executa a query!
    private function execute() {
        $this->connect();
        try {
            $this->update->execute(array_merge($this->dados, $this->places));
            $this->result = true;
        } catch (PDOException $e) {
            $this->result = null;
            WSErro("<b>Erro ao Ler:</b> {$e->getMessage()}", $e->getCode());
        }
    }

}
