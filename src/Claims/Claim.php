<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Concerns\Arrayable;

abstract class Claim
{
    use Arrayable;

    abstract public static function key(): string;
}
