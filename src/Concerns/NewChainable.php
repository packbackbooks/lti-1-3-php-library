<?php

namespace Packback\Lti1p3\Concerns;

trait NewChainable
{
    public static function new(...$sargs): static
    {
        return new static(...$sargs);
    }
}
