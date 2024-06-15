<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace HyperfTest\Feature\Infra\Repositories\Eloquent;

use App\Domain\Entities\User;
use App\Infra\Factories\UserFactory;
use App\Infra\Factories\WalletFactory;
use App\Infra\Repositories\Eloquent\UserRepository;
use Faker\Factory;
use Faker\Generator;
use Hyperf\Database\Exception\QueryException;
use Hyperf\DbConnection\Db;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(UserRepository::class), CoversClass(WalletFactory::class)]
class UserRepositoryTest extends TestCase
{
    protected UserRepository $sut;
    protected Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new UserRepository();
        $this->faker = Factory::create();
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
                    'id'        => $faker->uuid(),
                    'name'      => $faker->name(),
                    'user_type' => 'user',
                    'email'     => 'any@email.com',
                    'password'  => $faker->password(),
                    'identify'  => '123as56789',
                    'wallet_id' => $faker->uuid(),
                ],
            ]
        ];
    }

    #[DataProvider('userDataProvider')]
    #[Test]
    public function test_it_should_be_given_name_is_equal(array $data): void
    {
        (new WalletFactory())->create([
            'id' => $data['wallet_id'],
        ]);

        $user = $this->sut->store($data);

        $this->assertEquals($data['name'], $user->name);
    }

    #[DataProvider('userDataProvider')]
    #[Test]
    public function test_store_throw_duplicate_email(array $data): void
    {
        $this->expectException(QueryException::class);

        (new WalletFactory())->create([
            'id' => $data['wallet_id'],
        ]);

        $this->sut->store(array_merge($data, ['email' => 'any_email']));

        $this->sut->store(array_merge($data, ['email' => 'any_email', 'identify' => 'any_identify']));
    }

    #[DataProvider('userDataProvider')]
    #[Test]
    public function test_store_throw_duplicate_identify(array $data): void
    {
        $this->expectException(QueryException::class);

        (new WalletFactory())->create([
            'id' => $data['wallet_id'],
        ]);

        $this->sut->store(array_merge($data, [
            'identify' => 'any_identify'
        ]));

        $this->sut->store(array_merge($data, [
            'identify' => 'any_identify',
            'email'    => 'any_email'
        ]));
    }

    #[Test]
    public function test_find_by_email_it_should_be_return_user(): void
    {
        $userStub = (new UserFactory())->create(['email' => 'any_email']);
        $user = $this->sut->findByEmail($userStub->email);

        self::assertInstanceOf(User::class, $user);
        self::assertIsString($user->id);
        self::assertEquals('any_email', $user->email);
    }

    #[Test]
    public function test_find_by_email_it_should_be_return_null(): void
    {
        $user = $this->sut->findByEmail('invalid_email');

        self::assertNull($user);
    }

    #[Test]
    public function test_find_by_identify_it_should_be_return_user(): void
    {
        $userStub = (new UserFactory())->create(['identify' => 'any']);
        $user = $this->sut->findByIdentify($userStub->identify);

        self::assertInstanceOf(User::class, $user);
        self::assertIsString($user->id);
        self::assertEquals('any', $user->identify);
    }

    #[Test]
    public function test_find_by_identify_it_should_be_return_null(): void
    {
        $user = $this->sut->findByIdentify('invalid_identify');

        self::assertNull($user);
    }

    #[Test]
    public function test_show_it_should_be_return_user(): void
    {
        $userStub = (new UserFactory())->create();

        $user = $this->sut->show($userStub->id);

        self::assertInstanceOf(User::class, $user);
        self::assertEquals($userStub->id, $user->id);
    }

    #[Test]
    public function test_show_it_should_be_return_null(): void
    {
        $user = $this->sut->show('any_id');

        self::assertNull($user);
    }

    #[Test]
    public function test_lists_it_should_be_return_user(): void
    {
        $user = (new UserFactory())->create();

        $users = $this->sut->lists();

        self::assertCount(1, $users);
        self::assertEquals($user->id, $users[0]->id);
    }

    #[Test]
    public function test_lists_it_should_be_return_null(): void
    {
        $users = $this->sut->lists();

        self::assertCount(0, $users);
    }

    #[Test]
    public function test_credentials_it_should_be_return_user(): void
    {
        $user = (new UserFactory())->create();

        $credential = $this->sut->credentials($user->email, $user->password);

        self::assertEquals($credential->name, $user->name);
        self::assertIsString($user->id);
    }

    #[Test]
    public function test_credentials_it_should_be_return_null(): void
    {
        $credential = $this->sut->credentials('any_email', 'any_password');

        self::assertNull($credential);
    }
}
