<?php

namespace HyperfTest\E2E\User;

use App\Infra\Factories\UserFactory;
use Firebase\JWT\JWT;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Hyperf\Testing\Client;

use function Hyperf\Config\config;
use function Hyperf\Coroutine\run;
use function Hyperf\Support\make;

final class UserShowControllerTest extends TestCase
{
    private Client $client;
    private string $token;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->client = make(Client::class);
        $this->token = JWT::encode(['id' => 'any_id'], config('jwt.secret'), config('jwt.alg'));
    }

    #[Test]
    public function test_show_it_should_be_return_user(): void
    {
        run(function () {
            $user = (new UserFactory())->create();
            $response = $this->client->get(
                uri: "api/user/{$user->id}",
                headers: [
                    'Authorization' => 'Bearer ' . $this->token
                ]
            );

            self::assertEquals($user->id, $response['id']);
        });
    }

    #[Test]
    public function test_show_it_should_be_return_error_on_user_not_found(): void
    {
        run(function () {
            $response = $this->client->get(
                uri: 'api/user/invalid_id',
                headers: [
                    'Authorization' => 'Bearer ' . $this->token
                ]
            );

            self::assertEquals(['message' => 'User not found'], $response);
        });
    }

    #[Test]
    public function test_show_it_should_be_return_unauthorized_if_dont_send_token(): void
    {
        run(function () {
            $response = $this->client->get(
                uri: 'api/user/any_id'
            );

            self::assertEquals(['message' => 'Unauthorized'], $response);
        });
    }

    #[Test]
    public function test_show_it_should_be_return_unauthorized_if_dont_send_invalid_token(): void
    {
        run(function () {
            $response = $this->client->get(
                uri: 'api/user/any_id',
                headers: [
                    'Authorization' => 'Bearer any_token'
                ]
            );

            self::assertEquals(['message' => 'Invalid token'], $response);
        });
    }
}