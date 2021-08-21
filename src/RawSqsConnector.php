<?php

namespace PrimitiveSense\LaravelRawSqsConnector;

use Aws\Sqs\SqsClient;
use Illuminate\Queue\Connectors\ConnectorInterface;
use Illuminate\Support\Arr;

class RawSqsConnector implements ConnectorInterface
{
    const QUEUE_CONNECTOR_NAME = 'raw-sqs';

    /**
     * Establish a queue connection.
     *
     * @param  array<mixed> $config
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        $config = $this->getDefaultConfiguration($config);

        if (!class_exists($config['job_class'])) {
            throw new \InvalidArgumentException(
                'Raw SQS Connector - class ' . $config['job_class'] . ' does not exist'
            );
        }

        if (!is_subclass_of($config['job_class'], RawSqsJob::class)) {
            throw new \InvalidArgumentException(
                'Raw SQS Connector - ' . $config['job_class'] . ' must be a subclass of ' . RawSqsJob::class
            );
        }

        if ($config['key'] && $config['secret']) {
            $config['credentials'] = Arr::only($config, ['key', 'secret', 'token']);
        }

        $rawSqsQueue = new RawSqsQueue(
            new SqsClient($config),
            $config['queue'],
            $config['prefix'] ?? ''
        );

        if (class_exists($config['job_class'])) {
            $rawSqsQueue->setJobClass($config['job_class']);
        }

        return $rawSqsQueue;
    }

    /**
     * Get the default configuration for SQS.
     *
     * @param  array<mixed> $config
     * @return array<mixed>
     */
    protected function getDefaultConfiguration(array $config): array
    {
        return array_merge([
            'version' => 'latest',
            'http' => [
                'timeout' => 60,
                'connect_timeout' => 60,
            ],
        ], $config);
    }
}
