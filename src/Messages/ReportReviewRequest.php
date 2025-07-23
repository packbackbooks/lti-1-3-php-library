<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\Claims\Activity;
use Packback\Lti1p3\Claims\Asset;
use Packback\Lti1p3\Claims\AssetReportType;
use Packback\Lti1p3\Claims\ForUser;
use Packback\Lti1p3\Claims\Submission;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\MessageValidators\ReportReviewMessageValidator;

class ReportReviewRequest extends ResourceLinkRequest
{
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

    public function claimActivity(): Activity
    {
        return Activity::create($this);
    }

    public function claimForUser(): ForUser
    {
        return ForUser::create($this);
    }

    public function claimSubmission(): Submission
    {
        return Submission::create($this);
    }

    public function claimAsset(): Asset
    {
        return Asset::create($this);
    }

    public function claimAssetReportType(): AssetReportType
    {
        return AssetReportType::create($this);
    }
}
