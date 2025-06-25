<?php

namespace Packback\Lti1p3\MessageValidators;

use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;

class ReportReviewMessageValidator extends AbstractMessageValidator
{
    public static function getMessageType(): string
    {
        return LtiConstants::MESSAGE_TYPE_REPORTREVIEW;
    }

    /**
     * @throws LtiException
     */
    public static function validate(array $jwtBody): void
    {
        static::validateGenericMessage($jwtBody);

        if (empty($jwtBody[LtiConstants::AP_CLAIM_ACTIVITY])) {
            throw new LtiException('Missing Activity Claim');
        }
        if (empty($jwtBody[LtiConstants::AP_CLAIM_SUBMISSION])) {
            throw new LtiException('Missing Submission Claim');
        }
        if (empty($jwtBody[LtiConstants::AP_CLAIM_REPORT_TYPE])) {
            throw new LtiException('Missing Asset Report Type Claim');
        }
        if (empty($jwtBody[LtiConstants::AP_CLAIM_ASSET])) {
            throw new LtiException('Missing Asset Claim');
        }
    }
}
