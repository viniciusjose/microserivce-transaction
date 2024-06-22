<?php

namespace HyperfTest\E2E\User;

use App\Infra\Factories\UserFactory;
use Faker\Factory;
use Hyperf\DbConnection\Db;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Hyperf\Testing\Client;

use function Hyperf\Coroutine\run;
use function Hyperf\Support\make;

final class UserStoreControllerTest extends TestCase
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

    public static function userDataProvider(): array
    {
        $faker = Factory::create();

        return [
            [
                [
                    'id'       => $faker->uuid(),
                    'name'     => $faker->name(),
                    'userType' => 'user',
                    'email'    => $faker->valid()->email,
                    'password' => $faker->password(),
                    'identify' => (string)$faker->randomFloat(0, 10000, 100000),
                ],
            ]
        ];
    }

    #[Test]
    #[DataProvider('userDataProvider')]
    public function test_store_it_should_be_return_user_data(array $user): void
    {
        run(function () use ($user) {
            $response = $this->client->json('api/user', $user);

            self::assertEquals($user['name'], $response['name']);
            self::assertNotNull($response['id']);
        });
    }

    #[Test]
    #[DataProvider('userDataProvider')]
    public function test_store_it_should_be_return_required_field_name(array $user): void
    {
        run(function () use ($user) {
            $response = $this->client->json('api/user', [
                'userType' => $user['userType'],
                'email'    => $user['email'],
                'password' => $user['password'],
                'identify' => $user['identify'],
            ]);

            self::assertNull($response);
        });
    }

    #[Test]
    #[DataProvider('userDataProvider')]
    public function test_store_it_should_be_return_null_on_duplicate_user(array $user): void
    {
        run(function () use ($user) {
            (new UserFactory())->create(['email' => $user['email'], 'identify' => '123456']);

            $response = $this->client->json('api/user', $user);

            self::assertEquals(['message' => 'User already exists with this email'], $response);
        });
    }
}