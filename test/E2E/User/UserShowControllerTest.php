<?php

namespace HyperfTest\E2E\User;

use App\Infra\Factories\UserFactory;
use Faker\Factory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Hyperf\Testing\Client;

use function Hyperf\Coroutine\run;
use function Hyperf\Support\make;

final class UserShowControllerTest extends TestCase
{
    private Client $client;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->client = make(Client::class);
    }

    #[Test]
    public function test_show_it_should_be_return_user(): void
    {
        run(function () {
            $user = (new UserFactory())->create();
            $response = $this->client->get("api/user/{$user->id}");

            self::assertEquals($user->id, $response['id']);
        });
    }

    #[Test]
    public function test_show_it_should_be_return_error_on_user_not_found(): void
    {
        run(function () {
            $response = $this->client->get('api/user/invalid_id');

            self::assertEquals(['message' => 'User not found'], $response);
        });
    }
}