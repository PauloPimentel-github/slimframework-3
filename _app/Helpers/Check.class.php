<?php

/**
 * Check.class [ HELPER ]
 * Classe responável por manipular e validar dados do sistema!
 *
 * @copyright (c) 2018, Paulo Pimentel Aluno: UPINSIDE TECNOLOGIA
 */
class Check {

    private static $data;
    private static $format;

    /**
     * <b>Verifica E-mail:</b> Executa validação de formato de e-mail. Se for um email válido retorna true, ou retorna false.
     * @param STRING $Email = Uma conta de e-mail
     * @return BOOL = True para um email válido, ou false
     */
    public static function email($email) {
        self::$data = (string) $email;
        self::$format = '/[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\.\-]+\.[a-z]{2,4}$/';

        if (preg_match(self::$format, self::$data)):
            return true;
        else:
            return false;
        endif;
    }

    /**
     * <b>Tranforma Data:</b> Transforma uma data no formato DD/MM/YY em uma data no formato TIMESTAMP!
     * @param STRING $Name = Data em (d/m/Y) ou (d/m/Y H:i:s)
     * @return STRING = $Data = Data no formato timestamp!
     */
    public static function data($data) {
        self::$format = explode(' ', $data);
        self::$data = explode('/', self::$format[0]);

        if (empty(self::$format[1])):
            self::$format[1] = date('H:i:s');
        endif;

        self::$data = self::$data[2] . '-' . self::$data[1] . '-' . self::$data[0] . ' ' . self::$format[1];
        return self::$data;
    }
}
