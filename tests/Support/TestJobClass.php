<?php

namespace Tests\Support;

class TestJobClass
{
    public $data = [];
    public function __construct(array $data)
    {
        $this->data = $data;
    }
}
