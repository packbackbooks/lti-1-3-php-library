<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\Claims\Activity;
use Packback\Lti1p3\Claims\AssetReport;
use Packback\Lti1p3\Claims\AssetService;
use Packback\Lti1p3\Claims\Custom;
use Packback\Lti1p3\Claims\Notice as NoticeClaim;
use Packback\Lti1p3\Claims\Submission;
use Packback\Lti1p3\Messages\Concerns\HasActivityClaim;
use Packback\Lti1p3\MessageValidators\NoticeMessageValidator;

class Notice extends LtiMessage
{
    use HasActivityClaim;

    public static function requiredClaims(): array
    {
        return [
            Activity::claimKey(),
            AssetReport::claimKey(),
            AssetService::claimKey(),
            Custom::claimKey(),
            Submission::claimKey(),
        ];
    }

    public static function messageValidator(): string
    {
        return NoticeMessageValidator::class;
    }

    public function noticeClaim(): NoticeClaim
    {
        return NoticeClaim::create($this);
    }

    public function sub()
    {
        return $this->getBody()['sub'] ?? null;
    }
}
