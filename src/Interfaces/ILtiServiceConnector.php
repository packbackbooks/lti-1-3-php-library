<?php

namespace Packback\Lti1p3\Interfaces;

interface ILtiServiceConnector
{
    public function getAccessToken(array $scopes);

    public function post(string $url, string $params, array $scopes, string $contentType);

    public function get(string $url, array $scopes, string $contentType);
}
