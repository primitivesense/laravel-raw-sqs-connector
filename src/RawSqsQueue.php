<?php
namespace PrimitiveSense\LaravelRawSqsConnector;

use Illuminate\Queue\InvalidPayloadException;
use Illuminate\Queue\Jobs\SqsJob;
use Illuminate\Queue\SqsQueue;

class RawSqsQueue extends SqsQueue
{
    /**
     * @var string
     */
    protected $jobClass;

    /**
     * Pop the next job off of the queue.
     *
     * @param  string  $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    public function pop($queue = null)
    {
        $response = $this->sqs->receiveMessage([
            'QueueUrl' => $queue = $this->getQueue($queue),
            'AttributeNames' => ['All'],
        ]);

        if (! is_null($response['Messages']) && count($response['Messages']) > 0) {
            $message = $response['Messages'][0];

            $jobBody = json_decode($message['Body'], true);

            $jobClass = $this->getJobClass();

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
        $payload = json_encode($job->getData());

        return $this->sqs->sendMessage([
            'QueueUrl' => $this->getQueue($queue), 'MessageBody' => $payload,
        ])->get('MessageId');
    }

    /**
     * @param string $payload
     * @param null $queue
     * @param array<mixed> $options
     * @throws InvalidPayloadException
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        $p = json_decode($payload, true);
        $p = unserialize($p["data"]["command"]);
        $p = $p->getData();
        $p = json_encode($p);

        return $this->sqs->sendMessage([
            'QueueUrl' => $this->getQueue($queue), 'MessageBody' => $p,
        ])->get('MessageId');
        // throw new InvalidPayloadException('pushRaw is not permitted for raw-sqs connector');
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  mixed  $job
     * @param  mixed   $data
     * @param  string  $queue
     * @throws InvalidPayloadException
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        $payload = json_encode($job->getData());

        return $this->sqs->sendMessage([
            'QueueUrl' => $this->getQueue($queue),
            'MessageBody' => $payload,
            'DelaySeconds' => $this->secondsUntil($delay),
        ])->get('MessageId');
    }


    /**
     * @return string
     */
    public function getJobClass()
    {
        return $this->jobClass;
    }

    /**
     * @param string $jobClass
     * @return $this
     */
    public function setJobClass($jobClass)
    {
        $this->jobClass = $jobClass;
        return $this;
    }
}
