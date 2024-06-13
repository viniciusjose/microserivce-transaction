<?php

namespace HyperfTest\E2E\User;

use App\Infra\Factories\UserFactory;
use Hyperf\DbConnection\Db;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Hyperf\Testing\Client;

use function Hyperf\Coroutine\run;
use function Hyperf\Support\make;

final class UserListControllerTest extends TestCase
{
    private Client $client;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->client = make(Client::class);
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
            $response = $this->client->get("api/user");

            self::assertCount(1, $response['data']);
            self::assertEquals($user->id, $response['data'][0]['id']);
        });
    }

    #[Test]
    public function test_list_it_should_be_return_empty_array_without_users(): void
    {
        run(function () {
            $response = $this->client->get('api/user');

            self::assertCount(0, $response['data']);
        });
    }
}