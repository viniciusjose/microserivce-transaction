<?php

namespace App\Infra\Gateways;

use App\Domain\Contracts\Gateways\JwtInterface;

class Jwt implements JwtInterface
{
    public function __construct(
        protected array $config
    ) {
    }

    public function encode(array $data): string
    {
        $payload = [
            'iss'  => $this->config['iss'],
            'aud'  => $this->config['aud'],
            'iat'  => time(),
            'nbf'  => time(),
            'exp'  => time() + $this->config['ttl'],
            'data' => $data
        ];

        return \Firebase\JWT\JWT::encode($payload, $this->config['secret'], $this->config['alg']);
    }

    public function decode(string $token): array
    {
        // TODO: Implement decode() method.
    }
}