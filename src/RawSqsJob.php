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
    protected $data;

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
    public function getData()
    {
        return $this->data;
    }
}
