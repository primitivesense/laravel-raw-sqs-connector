<?php

namespace PrimitiveSense\LaravelRawSqsConnector;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;

class RawSqsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        /**
         * @var $queueManager QueueManager
         */
        $queueManager = $this->app->make(QueueManager::class);

        $queueManager->addConnector(RawSqsConnector::QUEUE_CONNECTOR_NAME, function () {
            return new RawSqsConnector;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
