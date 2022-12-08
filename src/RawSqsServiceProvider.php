<?php

namespace AgentSoftware\LaravelRawSqsConnector;

use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;

class RawSqsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        /**
         * @var $queueManager QueueManager
         */
        $queueManager = $this->app->make(QueueManager::class);

        $queueManager->addConnector(RawSqsConnector::QUEUE_CONNECTOR_NAME, function () {
            return new RawSqsConnector();
        });
    }
}
