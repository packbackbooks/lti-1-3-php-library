<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\Claims\Context;
use Packback\Lti1p3\Claims\OriginContexts;
use Packback\Lti1p3\Messages\Concerns\HasContextClaim;

class ContextCopyNotice extends Notice
{
    use HasContextClaim;

    public static function requiredClaims(): array
    {
        return [
            Context::claimKey(),
            OriginContexts::claimKey(),
        ];
    }

    public function originContextsClaim(): OriginContexts
    {
        return OriginContexts::create($this);
    }
}
