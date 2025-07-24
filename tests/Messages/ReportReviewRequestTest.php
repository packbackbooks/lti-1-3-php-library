<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Claims\Activity;
use Packback\Lti1p3\Claims\Asset;
use Packback\Lti1p3\Claims\AssetReportType;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\ForUser;
use Packback\Lti1p3\Claims\MessageType;
use Packback\Lti1p3\Claims\Submission;
use Packback\Lti1p3\Claims\TargetLinkUri;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\Messages\ReportReviewRequest;
use Tests\TestCase;

class ReportReviewRequestTest extends TestCase
{
    private $serviceConnectorMock;
    private $registrationMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceConnectorMock = Mockery::mock(ILtiServiceConnector::class);
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);
    }

    public function test_message_type_returns_report_review_constant()
    {
        $this->assertEquals('LtiReportReviewRequest', ReportReviewRequest::messageType());
        $this->assertEquals(LtiConstants::MESSAGE_TYPE_REPORTREVIEW, ReportReviewRequest::messageType());
    }

    public function test_required_claims_returns_expected_claims()
    {
        $expectedClaims = [
            MessageType::claimKey(),
            TargetLinkUri::claimKey(),
            Activity::claimKey(),
            ForUser::claimKey(),
            Submission::claimKey(),
            Asset::claimKey(),
            AssetReportType::claimKey(),
        ];

        $this->assertEquals($expectedClaims, ReportReviewRequest::requiredClaims());
    }

    public function test_activity_claim_returns_activity_instance()
    {
        $activity = ['id' => 'activity-456', 'type' => 'assessment'];
        $body = [Claim::ACTIVITY => $activity];
        $message = new ReportReviewRequest($this->serviceConnectorMock, $this->registrationMock, $body);

        $activityClaim = $message->activityClaim();

        $this->assertInstanceOf(Activity::class, $activityClaim);
        $this->assertEquals($activity, $activityClaim->getBody());
    }

    public function test_for_user_claim_returns_for_user_instance()
    {
        $forUser = ['user_id' => 'user-789'];
        $body = [Claim::FOR_USER => $forUser];
        $message = new ReportReviewRequest($this->serviceConnectorMock, $this->registrationMock, $body);

        $forUserClaim = $message->forUserClaim();

        $this->assertInstanceOf(ForUser::class, $forUserClaim);
        $this->assertEquals($forUser, $forUserClaim->getBody());
    }

    public function test_submission_claim_returns_submission_instance()
    {
        $submission = ['id' => 'submission-123', 'submitted_at' => '2024-01-01T00:00:00Z'];
        $body = [Claim::SUBMISSION => $submission];
        $message = new ReportReviewRequest($this->serviceConnectorMock, $this->registrationMock, $body);

        $submissionClaim = $message->submissionClaim();

        $this->assertInstanceOf(Submission::class, $submissionClaim);
        $this->assertEquals($submission, $submissionClaim->getBody());
    }

    public function test_asset_claim_returns_asset_instance()
    {
        $asset = ['id' => 'asset-456', 'url' => 'https://example.com/asset'];
        $body = [Claim::ASSET => $asset];
        $message = new ReportReviewRequest($this->serviceConnectorMock, $this->registrationMock, $body);

        $assetClaim = $message->assetClaim();

        $this->assertInstanceOf(Asset::class, $assetClaim);
        $this->assertEquals($asset, $assetClaim->getBody());
    }

    public function test_asset_report_type_claim_returns_asset_report_type_instance()
    {
        $assetReportType = ['type' => 'originality_report'];
        $body = [Claim::ASSETREPORT_TYPE => $assetReportType];
        $message = new ReportReviewRequest($this->serviceConnectorMock, $this->registrationMock, $body);

        $assetReportTypeClaim = $message->assetReportTypeClaim();

        $this->assertInstanceOf(AssetReportType::class, $assetReportTypeClaim);
        $this->assertEquals($assetReportType, $assetReportTypeClaim->getBody());
    }

    public function test_get_launch_id_returns_unique_string()
    {
        $message = new ReportReviewRequest($this->serviceConnectorMock, $this->registrationMock, []);

        $launchId = $message->getLaunchId();

        $this->assertStringStartsWith('lti1p3_launch_', $launchId);
    }
}
