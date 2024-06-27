<?php

namespace HyperfTest\Unit\Infra\Gateways;

use App\Domain\Entities\Transaction;
use App\Domain\Entities\TransactionNotification;
use App\Infra\Gateways\TransactionNotifyClient;
use Carbon\Carbon;
use Decimal\Decimal;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;

use function Hyperf\Support\make;

#[CoversClass(TransactionNotifyClient::class)]
#[UsesClass(Transaction::class), UsesClass(TransactionNotification::class)]
class TransactionNotifyClientTest extends TestCase
{
    protected TransactionNotifyClient $sut;

    protected ClientInterface $clientMock;
    protected LoggerInterface $loggerMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(ClientInterface::class);

        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->sut = $this->makeSut();
    }

    private function makeSut(): TransactionNotifyClient
    {
        return new TransactionNotifyClient(
            $this->clientMock,
            $this->loggerMock
        );
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function test_it_should_be_transaction_to_be_authorized(): void
    {
        $notification = new TransactionNotification(
            transaction_id: 'any_id',
            date: Carbon::now(),
            id: 'any_id'
        );

        $isAuthorized = $this->sut->notify($notification);

        self::assertTrue($isAuthorized);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     * @throws Exception
     */
    public function test_it_should_be_transaction_dont_be_authorized(): void
    {
        $this->clientMock = $this->createMock(ClientInterface::class);
        $this->clientMock->method('request')
            ->willThrowException(
                new ClientException(
                    'Error',
                    $this->createMock(RequestInterface::class),
                    $this->createMock(ResponseInterface::class)
                )
            );

        $notification = new TransactionNotification(
            transaction_id: 'any_id',
            date: Carbon::now(),
            id: 'any_id'
        );

        $sut = $this->makeSut();
        $isAuthorized = $sut->notify($notification);

        self::assertFalse($isAuthorized);
    }
}