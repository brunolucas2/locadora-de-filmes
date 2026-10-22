<?php
$envPath = dirname(__DIR__, 3) . '/.env';
$env = parse_ini_file($envPath);

$chave = sodium_hex2bin($env["CPF_SECRET"]);

function criptografarCPF(string $cpf): string
{
 global $chave;

 $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

 $criptografado = sodium_crypto_secretbox($cpf, $nonce, $chave);

 return base64_encode($nonce . $criptografado);
}

function descriptografarCPF(string $valor): string|false
{
 global $chave;

 $dados = base64_decode($valor, true);
 $tamanhoNonce = SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;

 if (
  $dados === false ||
  strlen($dados) < $tamanhoNonce + SODIUM_CRYPTO_SECRETBOX_MACBYTES
 ) {
  return false;
 }

 $nonce = substr($dados, 0, $tamanhoNonce);
 $criptografado = substr($dados, $tamanhoNonce);

 return sodium_crypto_secretbox_open($criptografado, $nonce, $chave);
}