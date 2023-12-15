<?php

namespace Lego\Units;

use Lego\Testing\MockMe;

/**
 * An abstract Job to be extended by every job.
 * Note that this job is self-handling which
 * means it will NOT be queued, rather
 * will have the "handle()" method
 * called instead.
 */
abstract class Job
{
    use MockMe;
}
