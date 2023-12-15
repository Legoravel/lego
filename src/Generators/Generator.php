<?php

namespace Lego\Generators;

use Lego\Filesystem;
use Lego\Finder;

class Generator
{
    use Finder;
    use Filesystem;

    /**
     * The current Laravel framework version
     * as defined in Foundation\Application::VERSION
     *
     * @param bool $majorOnly determines whether the needed version is only the major one
     * @return string
     */
    public function laravelVersion(bool $majorOnly = true): string
    {
        $version = app()->version();
        if ($majorOnly) {
            $version = explode('.', $version)[0];
        }

        return $version;
    }
}
