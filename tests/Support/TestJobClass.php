<?php

namespace Tests\Support;

use PrimitiveSense\LaravelRawSqsConnector\RawSqsJob;

class TestJobClass extends RawSqsJob
{
    public $data = [];
}
