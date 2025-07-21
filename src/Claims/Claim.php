<?php

namespace Packback\Lti1p3\Claims;

abstract class Claim
{
    abstract public static function key(): string;

    public function __construct(
        private $body
    ) {}

    public function getBody()
    {
        return $this->body;
    }
}
