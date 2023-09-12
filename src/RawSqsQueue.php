<?php
namespace PrimitiveSense\LaravelRawSqsConnector;

use DateInterval;
use DateTimeInterface;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\InvalidPayloadException;
use Illuminate\Queue\Jobs\SqsJob;
use Illuminate\Queue\SqsQueue;

class RawSqsQueue extends SqsQueue
{
    /**
     * @var string
     */
    protected string $jobClass;

    /**
     * Pop the next job off of the queue.
     *
     * @param null $queue
     * @return SqsJob|Job|null
     */
    public function pop($queue = null): SqsJob|Job|null
    {
        $response = $this->sqs->receiveMessage([
            'QueueUrl' => $queue = $this->getQueue($queue),
            'AttributeNames' => ['All'],
        ]);

        if (!is_null($response['Messages']) && count($response['Messages']) > 0) {
            $message = $response['Messages'][0];

            $jobBody = json_decode($message['Body'], true);
            if (is_null($jobBody)) {
                throw new InvalidPayloadException('Invalid or missing job body');
            }

            $jobClass = $jobBody['job'] ?? null;
            if (is_null($jobClass)) {
                $jobClass = $this->getDefaultJobClass();
            }

            if (!class_exists($jobClass)) {
                throw new InvalidPayloadException('Job class does not exist: ' . $jobClass);
            }

            $captureJob = new $jobClass($jobBody);

            $payload = $this->createPayload($captureJob, $queue, $jobBody);
            $message['Body'] = $payload;

            return new SqsJob(
                $this->container,
                $this->sqs,
                $message,
                $this->connectionName,
                $queue
            );
        }

        return null;
    }

    /**
     * @param object|string $job
     * @param string $data
     * @param null $queue
     * @throws InvalidPayloadException
     */
    public function push($job, $data = '', $queue = null)
    {
        throw new InvalidPayloadException('push is not permitted for raw-sqs connector');
    }

    /**
     * @param string $payload
     * @param null $queue
     * @param array $options
     * @throws InvalidPayloadException
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        throw new InvalidPayloadException('pushRaw is not permitted for raw-sqs connector');
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param  DateTimeInterface|DateInterval|int  $delay
     * @param  string  $job
     * @param  mixed   $data
     * @param  string  $queue
     * @throws InvalidPayloadException
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        throw new InvalidPayloadException('later is not permitted for raw-sqs connector');
    }

    /**
     * @return string
     */
    public function getDefaultJobClass(): string
    {
        return $this->jobClass;
    }

    /**
     * @param string $jobClass
     * @return $this
     */
    public function setDefaultJobClass(string $jobClass): static
    {
        $this->jobClass = $jobClass;
        return $this;
    }
}
