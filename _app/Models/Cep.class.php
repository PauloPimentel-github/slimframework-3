<?php

class Cep
{
	private $service;
	private $callback;
	private $params;

	public function __construct($params)
	{
        $this->params = $this->validateCep($params);
		$this->service = "https://viacep.com.br/ws/{$this->params}/json";
        $this->getCep();
	}

    /**
    * <b>getCallback</b>: Método responsável por retornar o objeto da comunicação REST
    */
    public function getCallback()
    {
        return $this->callback;
    }

    private function validateCep($cep)
    {
        $this->params = $cep;
        $this->params = trim($this->params);
        $this->params = strip_tags($this->params);
        $this->params = str_replace(array('.', '-', '/'), "", $this->params);

        if (preg_match("^[0-9]{5}-[0-9]{3}$^", $this->params)) {
            return false;

        } elseif(strlen($this->params) < 8 || strlen($this->params) > 8) {
            return false;

        } else {
            return $this->params;
        }
    }

    /**
    * <b>get</b>: Método responsável por resgatar informações da comunicação REST com o VIACEP
    */
    private function getCep()
    {
        //inicia uma conexão com o viacep
        $ch = curl_init($this->service);
        //seta a configurações do curl para uma requisição get
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //seta as configurações do curl, setando o cabeçalho para json, ou seja, os dados serão retornados no formato json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json', 'Accept: application/charset=UTF-8'));
        //atribui o retorno da busca dos dados do via cep e atribui na variável $callback no formato de objeto
        $this->callback = (array) json_decode(curl_exec($ch));
        //fecha a conexão com o serviço
        curl_close($ch);
    }

}
