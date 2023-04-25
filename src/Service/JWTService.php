<?php

declare(strict_types=1);

namespace App\Service;

use DateTimeImmutable;
use Nette\Utils\Strings;

final class JWTService
{
    // On génère le token
    /**
     * Génération du JWT
     * @param array<string> $header 
     * @param array<string> $payload 
     */
    public function generate(array $header, array $payload, string $secret, int $validity = 10800): string
    {
        if($validity > 0){
            $now = new DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;
    
            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        }

        // On encode en base64
        $base64Header = base64_encode((string) json_encode($header, JSON_THROW_ON_ERROR));
        $base64Payload = base64_encode((string) json_encode($payload, JSON_THROW_ON_ERROR));

        // On "nettoie" les valeurs encodées (retrait des +, / et =)
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

        // On génère la signature
        $secret = base64_encode($secret);

        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);

        $base64Signature = base64_encode($signature);

        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);

        // On crée le token
        $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;

        return $jwt;
    }

    //On vérifie que le token est valide (correctement formé)

    public function isValid(string $token): bool
    {
        ///** @var null|array $result */
        /* $result =  */Strings::match(
            $token,
            '#^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$#',
            //'/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
        ) ;
        
        return true;
    }

    // On récupère le Payload
    /** @return  array<string> $payload */
    public function getPayload(string $token): array
    {
        // On démonte le token
        $array = explode('.', $token);

        // On décode le Payload
        /** @var array<string> $payload */
        $payload = json_decode((string) base64_decode($array[1], true), true, 512, JSON_THROW_ON_ERROR);

        return $payload;
    }

    // On récupère le Header
    /** @return  array<string> $header */
    public function getHeader(string $token): array
    {
        // On démonte le token
        $array = explode('.', $token);

        // On décode le Header
        /** @var array<string> $header */
        $header = json_decode((string) base64_decode($array[0], true), true, 512, JSON_THROW_ON_ERROR);

        return $header;
    }

    // On vérifie si le token a expiré
    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);

        $now = new DateTimeImmutable();

        return $payload['exp'] < $now->getTimestamp();
    }

    // On vérifie la signature du Token
    public function check(string $token, string $secret): bool
    {
        // On récupère le header et le payload
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        // On régénère un token
        $verifToken = $this->generate($header, $payload, $secret, 0);

        return $token === $verifToken;
    }
}