<?php

namespace Lego\Units;

use Lego\Bus\UnitDispatcher;
use Lego\Testing\MockMe;

abstract class Feature
{
    use MockMe;
    use UnitDispatcher;
}
