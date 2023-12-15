<?php

namespace Lego\Events;

class JobStarted
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var array
     */
    public array $arguments;

    /**
     * JobStarted constructor.
     *
     * @param string $name
     * @param array $arguments
     */
    public function __construct(string $name, array $arguments = [])
    {
        $this->name = $name;
        $this->arguments = $arguments;
    }
}
