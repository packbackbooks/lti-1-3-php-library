<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\Claims\Activity;
use Packback\Lti1p3\Claims\AssetReport;
use Packback\Lti1p3\Claims\AssetService;
use Packback\Lti1p3\Claims\Custom;
use Packback\Lti1p3\Claims\Notice as NoticeClaim;
use Packback\Lti1p3\Claims\Submission;
use Packback\Lti1p3\Messages\Concerns\HasActivityClaim;

class AssetProcessorSubmissionNotice extends Notice
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

    public function noticeClaim(): NoticeClaim
    {
        return NoticeClaim::create($this);
    }

    public function assetReportClaim(): AssetReport
    {
        return AssetReport::create($this);
    }

    public function assetServiceClaim(): AssetService
    {
        return AssetService::create($this);
    }

    public function submissionClaim(): Submission
    {
        return Submission::create($this);
    }

    public function customClaim(): Custom
    {
        return Custom::create($this);
    }
}
