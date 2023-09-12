<?php

namespace PrimitiveSense\LaravelRawSqsConnector;

use Aws\Sqs\SqsClient;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Queue\Connectors\ConnectorInterface;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class RawSqsConnector implements ConnectorInterface
{
    const QUEUE_CONNECTOR_NAME = 'raw-sqs';

    /**
     * Establish a queue connection.
     *
     * @param array $config
     * @return RawSqsQueue|Queue
     */
    public function connect(array $config): RawSqsQueue|Queue
    {
        $config = $this->getDefaultConfiguration($config);

        if (!class_exists($config['default_job_class'])) {
            throw new InvalidArgumentException(
                'Raw SQS Connector - class ' . $config['job_class'] . ' does not exist'
            );
        }

        if (!is_subclass_of($config['default_job_class'], RawSqsJob::class)) {
            throw new InvalidArgumentException(
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

        if (isset($config['job_prefix'])) {
            $rawSqsQueue->setJobPrefix($config['job_prefix']);
        }

        if (class_exists($config['default_job_class'])) {
            $rawSqsQueue->setDefaultJobClass($config['default_job_class']);
        }

        return $rawSqsQueue;
    }

    /**
     * Get the default configuration for SQS.
     *
     * @param array $config
     * @return array
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
