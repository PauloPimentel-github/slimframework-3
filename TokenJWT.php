<?php

/**
 * Classe responsável por gerar token JWT
 */
class TokenJWT
{
    private $name;
    private $email;
    private $key;
    private $header;
    private $payload;
    private $signature;
    private $token;

    public function __construct(string $name, string $email, string $key)
    {
        $this->name = $name;
        $this->email = $email;
        $this->key = $key;
    }

    public function getToken() : string
    {
        $this->header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        $this->header = json_encode($this->header);
        $this->header = base64_encode($this->header);

        $this->payload = [
            'iss' => 'localhost',
            'user_name' => $this->name,
            'user_email' => $this->email
        ];

        $this->payload = json_encode($this->payload);
        $this->payload = base64_encode($this->payload);

        $this->signature = hash_hmac("sha256", "{$this->header}.{$this->payload}", $this->key, true);

        $this->signature = base64_encode($this->signature);

        $this->token = "{$this->header}.{$this->payload}.{$this->signature}";

        return $this->token;
    }
}
