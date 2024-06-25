<?php

namespace App\Adapters\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Hyperf\Context\Context;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function Hyperf\Config\config;

class AuthMiddleware implements MiddlewareInterface
{

    public function __construct(protected HttpResponse $response)
    {
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authorization = $request->getHeaderLine('Authorization');

        if (!$authorization || !preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
            return $this->response->withStatus(401)->json(['message' => 'Unauthorized']);
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key(config('jwt.secret'), config('jwt.alg')));
            Context::set('user', (array)$decoded);
        } catch (\Exception $e) {
            return $this->response->withStatus(401)->json(['message' => 'Invalid token']);
        }

        return $handler->handle($request);
    }
}