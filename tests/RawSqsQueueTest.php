<?php

namespace Tests;

use Aws\Sqs\SqsClient;
use Illuminate\Container\Container;
use Illuminate\Queue\InvalidPayloadException;
use Mockery;
use PHPUnit\Framework\TestCase;
use PrimitiveSense\LaravelRawSqsConnector\RawSqsQueue;
use Tests\Support\TestJobClass;

class RawSqsQueueTest extends TestCase
{
    public function testPopShouldReturnNewSqsJob()
    {
        $firstName = 'Primitive';
        $lastName = 'Sense';

        $sqsReturnMessage = [
            'Body' => json_encode([
               'first_name' => $firstName,
               'last_name' => $lastName
            ])
        ];

        $sqsClientMock = Mockery::mock(SqsClient::class);
        $sqsClientMock->shouldReceive('receiveMessage')
            ->andReturn([
                'Messages' => [
                    $sqsReturnMessage
                ]
            ]);


        $rawSqsQueue = new RawSqsQueue(
            $sqsClientMock,
            'default',
            'prefix'
        );

        $container = Mockery::mock(Container::class);
        $rawSqsQueue->setContainer($container);
        $rawSqsQueue->setJobClass(TestJobClass::class);
        $jobPayload = $rawSqsQueue->pop()->payload();

        $this->assertSame($jobPayload['displayName'], TestJobClass::class);
        $this->assertSame($jobPayload['job'], 'Illuminate\Queue\CallQueuedHandler@call');
        $this->assertSame($jobPayload['data']['commandName'], TestJobClass::class);

        $testJob = unserialize($jobPayload['data']['command']);
        $this->assertSame($testJob->data['first_name'], $firstName);
        $this->assertSame($testJob->data['last_name'], $lastName);
    }

    public function testPopShouldReturnNullIfMessagesAreNull()
    {
        $sqsClientMock = Mockery::mock(SqsClient::class);
        $sqsClientMock->shouldReceive('receiveMessage')
            ->andReturn([
                'Messages' => null
            ]);


        $rawSqsQueue = new RawSqsQueue(
            $sqsClientMock,
            'default',
            'prefix'
        );

        $container = Mockery::mock(Container::class);
        $rawSqsQueue->setContainer($container);
        $rawSqsQueue->setJobClass(TestJobClass::class);
        $this->assertNull($rawSqsQueue->pop());
    }

    public function testPushShouldthrowInvalidPayLoadException()
    {
        $this->expectException(InvalidPayloadException::class);
        $this->expectExceptionMessage('push is not permitted for raw-sqs connector');

        $sqsClientMock = Mockery::mock(SqsClient::class);

        $rawSqsQueue = new RawSqsQueue(
            $sqsClientMock,
            'default',
            'prefix'
        );

        $rawSqsQueue->push(null, null, null);
    }

    public function testPushRawShouldThrowInvalidPayLoadException()
    {
        $this->expectException(InvalidPayloadException::class);
        $this->expectExceptionMessage('pushRaw is not permitted for raw-sqs connector');

        $sqsClientMock = Mockery::mock(SqsClient::class);

        $rawSqsQueue = new RawSqsQueue(
            $sqsClientMock,
            'default',
            'prefix'
        );

        $rawSqsQueue->pushRaw(null, null, []);
    }

    public function testLaterShouldThrowInvalidPayLoadException()
    {
        $this->expectException(InvalidPayloadException::class);
        $this->expectExceptionMessage('later is not permitted for raw-sqs connector');

        $sqsClientMock = Mockery::mock(SqsClient::class);

        $rawSqsQueue = new RawSqsQueue(
            $sqsClientMock,
            'default',
            'prefix'
        );

        $rawSqsQueue->later(null, null);
    }
}
