<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\MessageValidators\ReportReviewMessageValidator;

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
            Claim::ACTIVITY,
            Claim::SUBMISSION,
            Claim::ASSET,
            Claim::ASSETREPORT_TYPE,
        ];
    }

    public static function optionalClaims(): array
    {
        return [
            Claim::FOR_USER,
            Claim::CONTEXT,
            Claim::TARGET_LINK_URI,
            Claim::PLATFORMNOTIFICATIONSERVICE,
        ];
    }

    public static function messageValidator(): string
    {
        return ReportReviewMessageValidator::class;
    }
}
