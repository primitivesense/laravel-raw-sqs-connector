<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use AgentSoftware\LaravelRawSqsConnector\RawSqsJob;

class RawSqsJobTest extends TestCase
{
    public function testGettersSetters(): void
    {
        $data = ['first_name' => 'Primitive'];
        $rawSqsJob = new RawSqsJob($data);
        $this->assertSame($data, $rawSqsJob->getData());
    }
}
