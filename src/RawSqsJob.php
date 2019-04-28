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
     * @param mixed $data
     */
    public function setData($data = null)
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
