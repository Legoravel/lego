<?php

namespace Lego\Entities;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @property-read string title
 * @property-read string className
 * @property-read string service
 * @property-read string file
 * @property-read string realPath
 * @property-read string relativePath
 * @property-read string content
 */
class Entity implements Arrayable
{
    protected array $attributes = [];

    /**
     * Get the array representation of this instance.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Set the attributes for this component.
     */
    protected function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * Get an attribute's value if found.
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->attributes[$key] ?? null;
    }
}
