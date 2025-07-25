<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Claims\Activity;
use Packback\Lti1p3\Claims\AssetReport;
use Packback\Lti1p3\Claims\AssetService;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\Custom;
use Packback\Lti1p3\Claims\Notice;
use Packback\Lti1p3\Claims\Submission;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Messages\AssetProcessorSubmissionNotice;
use Tests\TestCase;

class AssetProcessorSubmissionNoticeTest extends TestCase
{
    private $serviceConnectorMock;
    private $registrationMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceConnectorMock = Mockery::mock(ILtiServiceConnector::class);
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);
    }

    public function test_required_claims_returns_expected_claims()
    {
        $expectedClaims = [
            Activity::claimKey(),
            AssetReport::claimKey(),
            AssetService::claimKey(),
            Custom::claimKey(),
            Submission::claimKey(),
        ];

        $this->assertEquals($expectedClaims, AssetProcessorSubmissionNotice::requiredClaims());
    }

    public function test_activity_claim_returns_activity_instance()
    {
        $activity = ['id' => 'activity-999', 'type' => 'submission'];
        $body = [Claim::ACTIVITY => $activity];
        $message = new AssetProcessorSubmissionNotice($this->serviceConnectorMock, $this->registrationMock, $body);

        $activityClaim = $message->activityClaim();

        $this->assertInstanceOf(Activity::class, $activityClaim);
        $this->assertEquals($activity, $activityClaim->getBody());
    }

    public function test_notice_claim_returns_notice_instance()
    {
        $notice = ['type' => 'submission_complete', 'message' => 'Processing completed'];
        $body = [Claim::NOTICE => $notice];
        $message = new AssetProcessorSubmissionNotice($this->serviceConnectorMock, $this->registrationMock, $body);

        $noticeClaim = $message->noticeClaim();

        $this->assertInstanceOf(Notice::class, $noticeClaim);
        $this->assertEquals($notice, $noticeClaim->getBody());
    }

    public function test_asset_report_claim_returns_asset_report_instance()
    {
        $assetReport = ['report_id' => 'report-789', 'status' => 'complete'];
        $body = [Claim::ASSETREPORT => $assetReport];
        $message = new AssetProcessorSubmissionNotice($this->serviceConnectorMock, $this->registrationMock, $body);

        $assetReportClaim = $message->assetReportClaim();

        $this->assertInstanceOf(AssetReport::class, $assetReportClaim);
        $this->assertEquals($assetReport, $assetReportClaim->getBody());
    }

    public function test_asset_service_claim_returns_asset_service_instance()
    {
        $assetService = ['service_url' => 'https://example.com/asset-service', 'token' => 'token123'];
        $body = [Claim::ASSETSERVICE => $assetService];
        $message = new AssetProcessorSubmissionNotice($this->serviceConnectorMock, $this->registrationMock, $body);

        $assetServiceClaim = $message->assetServiceClaim();

        $this->assertInstanceOf(AssetService::class, $assetServiceClaim);
        $this->assertEquals($assetService, $assetServiceClaim->getBody());
    }

    public function test_submission_claim_returns_submission_instance()
    {
        $submission = ['id' => 'submission-456', 'status' => 'submitted'];
        $body = [Claim::SUBMISSION => $submission];
        $message = new AssetProcessorSubmissionNotice($this->serviceConnectorMock, $this->registrationMock, $body);

        $submissionClaim = $message->submissionClaim();

        $this->assertInstanceOf(Submission::class, $submissionClaim);
        $this->assertEquals($submission, $submissionClaim->getBody());
    }

    public function test_custom_claim_returns_custom_instance()
    {
        $custom = ['custom_param' => 'custom_value', 'another_param' => 'another_value'];
        $body = [Claim::CUSTOM => $custom];
        $message = new AssetProcessorSubmissionNotice($this->serviceConnectorMock, $this->registrationMock, $body);

        $customClaim = $message->customClaim();

        $this->assertInstanceOf(Custom::class, $customClaim);
        $this->assertEquals($custom, $customClaim->getBody());
    }

    public function test_sub_method_returns_notice_type_from_body()
    {
        $body = ['sub' => 'LtiAssetProcessorSubmissionNotice'];
        $message = new AssetProcessorSubmissionNotice($this->serviceConnectorMock, $this->registrationMock, $body);

        $sub = $message->sub();

        $this->assertEquals('LtiAssetProcessorSubmissionNotice', $sub);
    }
}
