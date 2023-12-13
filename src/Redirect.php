<?php

namespace Packback\Lti1p3;

class Redirect
{
    private $location;
    private $referer_query;
    private static $CAN_302_COOKIE = 'LTI_302_Redirect';

    public function __construct(string $location, ?string $referer_query = null)
    {
        $this->location = $location;
        $this->referer_query = $referer_query;
    }

    public function doRedirect()
    {
        header('Location: '.$this->location, true, 302);
        exit;
    }

    public function getRedirectUrl()
    {
        return $this->location;
    }
}
