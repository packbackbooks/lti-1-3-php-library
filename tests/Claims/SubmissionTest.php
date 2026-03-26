<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\Submission;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class SubmissionTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_submission_constant()
    {
        $this->assertEquals(Claim::SUBMISSION, Submission::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = ['id' => 'submission-123', 'status' => 'submitted', 'timestamp' => '2024-01-01T12:00:00Z'];
        $submission = new Submission($body);

        $this->assertEquals($body, $submission->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $submissionData = ['id' => 'submission-456', 'grade' => 85];
        $messageBody = [Claim::SUBMISSION => $submissionData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $submission = Submission::create($this->messageMock);

        $this->assertInstanceOf(Submission::class, $submission);
        $this->assertEquals($submissionData, $submission->getBody());
    }

    public function test_id_method_returns_id_from_body()
    {
        $submissionId = 'submission-789';
        $body = ['id' => $submissionId, 'status' => 'graded'];
        $submission = new Submission($body);

        $this->assertEquals($submissionId, $submission->id());
    }
}
