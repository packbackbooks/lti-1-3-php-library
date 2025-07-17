<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\MessageValidators\ReportReviewValidator;

class ReportReviewRequest extends LaunchMessage
{
    public static function messageType(): string
    {
        return LtiContstants::MESSAGE_TYPE_REPORTREVIEW;
    }

    /**
     * @todo add these
     */
    public static function requiredClaims(): array
    {
        return [
        ];
    }

    public static function optionalClaims(): array
    {
        return [
            LtiConstants::FOR_USER,
            LtiConstants::AP_CLAIM_SUBMISSION,
            LtiConstants::AP_CLAIM_ASSET,
            LtiConstants::AP_CLAIM_ASSETREPORT_TYPE,
        ];
    }

    protected function messageValidator(): string
    {
        return ReportReviewValidator::class;
    }
}
