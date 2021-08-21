# Laravel Raw SQS Connector

[![Build Status](https://travis-ci.org/primitivesense/laravel-raw-sqs-connector.svg?branch=master)](https://travis-ci.org/primitivesense/laravel-raw-sqs-connector)
[![Maintainability](https://api.codeclimate.com/v1/badges/079c45048f9e349e67bb/maintainability)](https://codeclimate.com/github/primitivesense/laravel-raw-sqs-connector/maintainability)
[![Latest Stable Version](https://poser.pugx.org/primitivesense/laravel-raw-sqs-connector/version)](https://packagist.org/packages/primitivesense/laravel-raw-sqs-connector)
[![Total Downloads](https://poser.pugx.org/primitivesense/laravel-raw-sqs-connector/downloads)](https://packagist.org/packages/primitivesense/laravel-raw-sqs-connector)
[![License](https://poser.pugx.org/primitivesense/laravel-raw-sqs-connector/license)](https://packagist.org/packages/primitivesense/laravel-raw-sqs-connector)

## About
The purpose of this package is to allow you to consume raw messages produced outside of Laravel from AWS SQS to then be handled natively within Laravel's Queue and Job system. 

- Integrates natively into Laravel's Queue system, leveraging all the existing functionality.
- It extends base Laravel SQS functionality, only overriding a small subset of SQS methods.
- Its used in production.
- Comprehensive documentation.
- Full suite of unit tests.


This library was originally built to allow the submission of jobs from AWS Lambda into Laravel.

## Dependencies

* PHP >= 7.3
* Laravel >= 8.0

## Installation via Composer

To install:

```
composer require primitivesense/laravel-raw-sqs-connector
```

## How to use

Add the Service Provider into `config/app.php` like so:

```
'providers' => [
    '...',
    '\PrimitiveSense\LaravelRawSqsConnector\RawSqsServiceProvider'
];
```

Create a new job like so:

```
<?php

namespace App\Jobs;

use PrimitiveSense\LaravelRawSqsConnector\RawSqsJob;

class ExampleRawSqsJob extends RawSqsJob
{
    public function handle()
    {
        $yourRawMessage = $this->data;
    }
}
```

`RawSqsJob` is a base class that has all the required traits to allow for Laravel's Queue System to handle it correctly.

Your raw message from SQS can be accessed via `$this->data`, magic!

To then configure this within `config/queue.php` add the block below:

```
'your-raw-sqs-queue' => [
    'driver' => 'raw-sqs',
    'job_class' => \App\Jobs\ExampleRawSqsJob::class,
    'key' => env('SQS_KEY', 'your-public-key'),
    'secret' => env('SQS_SECRET', 'your-secret-key'),
    'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
    'queue' => env('SQS_QUEUE', 'your-queue-name'),
    'region' => env('SQS_REGION', 'us-east-1'),
],
```

`your-raw-sqs-queue` is simply your custom message queue, the important bits to note are `driver` and `job_class`. `driver` is simply this packages `raw-sqs` connector and `job_class` just tells Laravel Queue which job to deletegate the raw message too.

Then simply invoke the below to start the queue

```
php artisan queue:work your-raw-sqs-queue
```

It does give you the flexibiity to push multiple messages into the queue but they will only be proccessed by that one job.

## How it works
There is a new `RawSqsConnector` and `RawSqsQueue`. The `RawSqsConnector` handles the construction of the `RawSqsQueue` class which responsibility is to process/submit the messages to SQS. There is then a base `RawSqsJob` class which can be extended from which has all the traits required for the Queue system to delegate and handle the job correctly.

`RawSqsQueue` extends Larvel's SQS Queue, overriding a few core methods, the `push` methods are disabled as the package is designed to consume jobs from SQS rather than push jobs onto the Queue. The main amount of work resides within the overriden `pop` method which processes the incoming message. This method has been extended to take the message and marshal it into a format that Laravel's queue system understands to then be later handled by the job it's self.


## Help!
If you have any issues please feel free to raise an issue!

## Contributing

Contributions are more than welcome, please feel free to raise a PR but please ensure:

- Complies with PSR-2 - `./vendor/bin/phpcs --standard=PSR2`
- Complies with the packages PHPStan Configuration - `./vendor/bin/phpstan analyse -l max -c phpstan.neon src` 


## License

The Laravel Raw SQS Connector is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
