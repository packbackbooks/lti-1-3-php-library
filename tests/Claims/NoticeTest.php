<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\Notice;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class NoticeTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_notice_constant()
    {
        $this->assertEquals(Claim::NOTICE, Notice::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = ['id' => 'notice-123', 'type' => 'grade_sync', 'timestamp' => '2024-01-01T12:00:00Z'];
        $notice = new Notice($body);

        $this->assertEquals($body, $notice->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $noticeData = ['id' => 'notice-456', 'type' => 'submission_review'];
        $messageBody = [Claim::NOTICE => $noticeData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $notice = Notice::create($this->messageMock);

        $this->assertInstanceOf(Notice::class, $notice);
        $this->assertEquals($noticeData, $notice->getBody());
    }

    public function test_id_method_returns_id_from_body()
    {
        $noticeId = 'notice-789';
        $body = ['id' => $noticeId, 'type' => 'context_copy'];
        $notice = new Notice($body);

        $this->assertEquals($noticeId, $notice->id());
    }

    public function test_type_method_returns_type_from_body()
    {
        $noticeType = 'grade_sync';
        $body = ['id' => 'notice-123', 'type' => $noticeType];
        $notice = new Notice($body);

        $this->assertEquals($noticeType, $notice->type());
    }

    public function test_timestamp_method_returns_timestamp_from_body()
    {
        $timestamp = '2024-01-01T12:00:00Z';
        $body = ['id' => 'notice-123', 'timestamp' => $timestamp];
        $notice = new Notice($body);

        $this->assertEquals($timestamp, $notice->timestamp());
    }
}
