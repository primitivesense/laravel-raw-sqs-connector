<?php

namespace PrimitiveSense\LaravelRawSqsConnector;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RawSqsJob implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var mixed
     */
    protected mixed $data;

    /**
     * RawSqsJob constructor.
     * @param null $data
     */
    public function __construct($data = null)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }
}
