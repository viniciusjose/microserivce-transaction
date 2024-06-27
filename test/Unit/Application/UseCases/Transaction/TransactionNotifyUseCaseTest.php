<?php

namespace HyperfTest\Unit\Application\UseCases\Transaction;

use App\Application\UseCases\Transaction\TransactionNotifyUseCase;
use App\Domain\Contracts\Gateways\TransactionNotificationClientInterface;
use App\Domain\Contracts\Gateways\UuidGeneratorInterface;
use App\Domain\Contracts\Repositories\Transaction\TransactionShowInterface;
use App\Domain\Contracts\Repositories\TransactionNotification\TransactionNotificationStoreInterface;
use App\Domain\DTO\Transaction\notify\TransactionNotifyInputDto;
use App\Domain\Entities\Transaction;
use App\Domain\Entities\TransactionNotification;
use App\Domain\Enums\StatusEnum;
use App\Domain\Exceptions\Transaction\TransactionNotFoundException;
use Carbon\Carbon;
use Decimal\Decimal;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

#[CoversClass(TransactionNotifyUseCase::class), CoversClass(TransactionNotifyInputDto::class)]
#[UsesClass(TransactionNotifyUseCase::class), UsesClass(TransactionNotifyInputDto::class)]
class TransactionNotifyUseCaseTest extends TestCase
{
    protected TransactionNotifyUseCase $sut;

    protected UuidGeneratorInterface $uuidGeneratorMock;
    protected TransactionShowInterface $transactionRepoMock;
    protected TransactionNotificationStoreInterface $transactionNotificationStoreMock;
    protected TransactionNotificationClientInterface $notificationClientMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->uuidGeneratorMock = $this->createMock(UuidGeneratorInterface::class);
        $this->uuidGeneratorMock->method('generate')->willReturn('uuid');

        $this->transactionRepoMock = $this->createMock(TransactionShowInterface::class);
        $this->transactionRepoMock->method('show')->willReturn(
            new Transaction(
                payerWalletId: 'any_uuid',
                payeeWalletId: 'any_payee_wallet_uuid',
                value: new Decimal('100'),
                date: Carbon::now(),
                id: 'uuid'
            )
        );

        $this->transactionNotificationStoreMock = $this->createMock(TransactionNotificationStoreInterface::class);

        $this->transactionNotificationStoreMock->method('store')->willReturn(
            new TransactionNotification(
                transaction_id: 'uuid',
                date: Carbon::now(),
                id: 'uuid',
                status: StatusEnum::DONE
            )
        );

        $this->notificationClientMock = $this->createMock(TransactionNotificationClientInterface::class);
        $this->notificationClientMock->method('notify')->willReturn(true);

        $this->sut = $this->makeSut();
    }

    private function makeSut(): TransactionNotifyUseCase
    {
        return new TransactionNotifyUseCase(
            $this->uuidGeneratorMock,
            $this->transactionRepoMock,
            $this->transactionNotificationStoreMock,
            $this->notificationClientMock
        );
    }

    public function test_it_should_be_return_true_when_user_has_notified(): void
    {
        $input = new TransactionNotifyInputDto(
            transaction_id: 'uuid',
            payee_id: 'payee_uuid',
            payer_id: 'payer_uuid',
            value: 100,
            date: Carbon::now()
        );

        $result = $this->sut->handle($input);

        self::assertTrue($result);
    }

    /**
     * @throws Exception
     */
    public function test_it_should_be_throw_if_notify_client_down(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Error on notify transaction');

        $this->notificationClientMock = $this->createMock(TransactionNotificationClientInterface::class);
        $this->notificationClientMock->method('notify')->willReturn(false);

        $input = new TransactionNotifyInputDto(
            transaction_id: 'uuid',
            payee_id: 'payee_uuid',
            payer_id: 'payer_uuid',
            value: 100,
            date: Carbon::now()
        );

        $sut = $this->makeSut();

        $sut->handle($input);
    }

    /**
     * @throws Exception
     */
    public function test_it_should_be_throw_if_transaction_not_found(): void
    {
        $this->expectException(TransactionNotFoundException::class);

        $this->transactionRepoMock = $this->createMock(TransactionShowInterface::class);
        $this->transactionRepoMock->method('show')->willReturn(null);

        $input = new TransactionNotifyInputDto(
            transaction_id: 'uuid',
            payee_id: 'payee_uuid',
            payer_id: 'payer_uuid',
            value: 100,
            date: Carbon::now()
        );

        $sut = $this->makeSut();

        $sut->handle($input);
    }
}