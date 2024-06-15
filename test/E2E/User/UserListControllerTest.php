<?php

namespace HyperfTest\E2E\User;

use App\Infra\Factories\UserFactory;
use Firebase\JWT\JWT;
use Hyperf\DbConnection\Db;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Hyperf\Testing\Client;

use function Hyperf\Config\config;
use function Hyperf\Coroutine\run;
use function Hyperf\Support\make;

final class UserListControllerTest extends TestCase
{
    private Client $client;
    private string $token;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->client = make(Client::class);
        $this->token = JWT::encode(['id' => 'any_id'], config('jwt.secret'), config('jwt.alg'));
    }

    protected function setUp(): void
    {
        parent::setUp();

        Db::beginTransaction();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Db::rollBack();
    }

    #[Test]
    public function test_list_it_should_be_return_users(): void
    {
        run(function () {
            $user = (new UserFactory())->create();
            $response = $this->client->get(
                uri: "api/user",
                headers: [
                    'Authorization' => 'Bearer ' . $this->token
                ]
            );

            self::assertCount(1, $response['data']);
            self::assertEquals($user->id, $response['data'][0]['id']);
        });
    }

    #[Test]
    public function test_list_it_should_be_return_empty_array_without_users(): void
    {
        run(function () {
            $response = $this->client->get(
                uri: "api/user",
                headers: [
                    'Authorization' => 'Bearer ' . $this->token
                ]
            );

            self::assertCount(0, $response['data']);
        });
    }

    #[Test]
    public function test_list_it_should_be_return_unauthorized_if_dont_send_token(): void
    {
        run(function () {
            $response = $this->client->get(
                uri: 'api/user'
            );

            self::assertEquals(['message' => 'Unauthorized'], $response);
        });
    }

    #[Test]
    public function test_list_it_should_be_return_unauthorized_if_dont_send_invalid_token(): void
    {
        run(function () {
            $response = $this->client->get(
                uri: 'api/user',
                headers: [
                    'Authorization' => 'Bearer any_token'
                ]
            );

            self::assertEquals(['message' => 'Invalid token'], $response);
        });
    }
}