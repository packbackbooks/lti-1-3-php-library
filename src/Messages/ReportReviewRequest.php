<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\Claims\Activity;
use Packback\Lti1p3\Claims\Asset;
use Packback\Lti1p3\Claims\AssetReportType;
use Packback\Lti1p3\Claims\ForUser;
use Packback\Lti1p3\Claims\Submission;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\Messages\Concerns\HasActivityClaim;
use Packback\Lti1p3\MessageValidators\ReportReviewMessageValidator;

class ReportReviewRequest extends ResourceLinkRequest
{
    use HasActivityClaim;

    public static function messageType(): string
    {
        return LtiConstants::MESSAGE_TYPE_REPORTREVIEW;
    }

    public static function requiredClaims(): array
    {
        return [
            ...parent::requiredClaims(),
            Activity::claimKey(),
            ForUser::claimKey(),
            Submission::claimKey(),
            Asset::claimKey(),
            AssetReportType::claimKey(),
        ];
    }

    public static function messageValidator(): string
    {
        return ReportReviewMessageValidator::class;
    }

    public function forUserClaim(): ForUser
    {
        return ForUser::create($this);
    }

    public function submissionClaim(): Submission
    {
        return Submission::create($this);
    }

    public function assetClaim(): Asset
    {
        return Asset::create($this);
    }

    public function assetReportTypeClaim(): AssetReportType
    {
        return AssetReportType::create($this);
    }
}
