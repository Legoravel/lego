<?php

namespace Lego\Events;

class FeatureStarted
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
     * FeatureStarted constructor.
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
