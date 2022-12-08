<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use AgentSoftware\LaravelRawSqsConnector\RawSqsConnector;
use AgentSoftware\LaravelRawSqsConnector\RawSqsQueue;
use Tests\Support\TestJobClass;

class RawSqsConnectorTest extends TestCase
{
    public function testConnectShouldReturnRawSqsQueue(): void
    {
        $rawSqsConnector = new RawSqsConnector();

        $config = [
            'key' => 'key',
            'secret' => 'secret',
            'region' => 'eu-west-2',
            'queue' => 'raw-sqs',
            'job_class' => TestJobClass::class
        ];

        $rawSqsQueue = $rawSqsConnector->connect($config);

        $this->assertInstanceOf(RawSqsQueue::class, $rawSqsQueue);
    }

    public function testShouldThrowInvalidArgumentExceptionIfClassDoesNotExist(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Raw SQS Connector - class class_that_does_not_exist does not exist');

        $rawSqsConnector = new RawSqsConnector();

        $config = [
            'job_class' => 'class_that_does_not_exist'
        ];

        $rawSqsQueue = $rawSqsConnector->connect($config);
        $this->assertInstanceOf(RawSqsQueue::class, $rawSqsQueue);
    }

    public function testShouldThrowInvalidArgumentExceptionIfClassDoesNotExtendRawSqsJob(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Raw SQS Connector - stdClass must be a subclass of AgentSoftware\LaravelRawSqsConnector\RawSqsJob'
        );

        $rawSqsConnector = new RawSqsConnector();

        $config = [
            'job_class' => \stdClass::class,
        ];

        $rawSqsQueue = $rawSqsConnector->connect($config);
        $this->assertInstanceOf(RawSqsQueue::class, $rawSqsQueue);
    }
}
