<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\LtiConstants;

class ReportReviewRequest extends LaunchMessage
{
    public static function messageType(): string
    {
        return LtiConstants::MESSAGE_TYPE_REPORTREVIEW;
    }

    /**
     * @todo add these
     */
    public static function requiredClaims(): array
    {
        return [
            LtiConstants::MESSAGE_TYPE,
            LtiConstants::AP_CLAIM_ACTIVITY,
            LtiConstants::AP_CLAIM_SUBMISSION,
            LtiConstants::AP_CLAIM_ASSET,
            LtiConstants::AP_CLAIM_REPORT_TYPE,
        ];
    }

    public static function optionalClaims(): array
    {
        return [
            LtiConstants::FOR_USER,
            LtiConstants::CONTEXT,
            LtiConstants::TARGET_LINK_URI,
            LtiConstants::PNS_CLAIM_SERVICE,
        ];
    }

    protected function messageValidator(): string
    {
        return ReportReviewValidator::class;
    }
}
