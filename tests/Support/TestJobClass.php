<?php

namespace Tests\Support;

use AgentSoftware\LaravelRawSqsConnector\RawSqsJob;

class TestJobClass extends RawSqsJob
{
    public mixed $data = [];
}
