<?php

namespace HyperfTest\Unit\Infra\Gateways;

use App\Domain\Entities\Transaction;
use App\Infra\Gateways\TransactionAuthorizeClient;
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

#[CoversClass(TransactionAuthorizeClient::class)]
#[UsesClass(Transaction::class)]
class TransactionAuthorizeClientTest extends TestCase
{
    protected TransactionAuthorizeClient $sut;

    protected ClientInterface $clientMock;
    protected LoggerInterface $loggerMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(ClientInterface::class);

        $mockResponse = $this->createMock(ResponseInterface::class);

        $mockStream = $this->createMock(StreamInterface::class);

        $mockStream->method('getContents')
            ->willReturn('{"status" : "success", "data" : { "authorization" : true }}');

        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $this->clientMock->method('request')
            ->willReturn($mockResponse);

        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->sut = $this->makeSut();
    }

    private function makeSut(): TransactionAuthorizeClient
    {
        return new TransactionAuthorizeClient(
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
        $transaction = new Transaction(
            payerWalletId: 'any_payer_wallet_id',
            payeeWalletId: 'any_payee_wallet_id',
            value: new Decimal(100),
            date: Carbon::now(),
            id: 'any_id'
        );

        $isAuthorized = $this->sut->authorize($transaction);

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
        $transaction = new Transaction(
            payerWalletId: 'any_payer_wallet_id',
            payeeWalletId: 'any_payee_wallet_id',
            value: new Decimal(100),
            date: Carbon::now(),
            id: 'any_id'
        );

        $sut = $this->makeSut();
        $isAuthorized = $sut->authorize($transaction);

        self::assertFalse($isAuthorized);
    }
}