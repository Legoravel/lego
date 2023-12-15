<?php

namespace Lego\Bus;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Collection;
use Lego\Events\FeatureStarted;

trait ServesFeatures
{
    use Marshal;
    use DispatchesJobs;

    /**
     * Serve the given feature with the given arguments.
     *
     * @param string $feature
     * @param  array  $arguments
     * @return mixed
     */
    public function serve(string $feature, array $arguments = [])
    {
        event(new FeatureStarted($feature, $arguments));

        return $this->dispatchSync($this->marshal($feature, new Collection(), $arguments));
    }
}
