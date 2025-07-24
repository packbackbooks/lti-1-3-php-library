<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Messages\Notice;
use Tests\TestCase;

class NoticeTest extends TestCase
{
    private $serviceConnectorMock;
    private $registrationMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceConnectorMock = Mockery::mock(ILtiServiceConnector::class);
        $this->registrationMock = Mockery::mock(ILtiRegistration::class);
    }

    private function createTestNotice(array $body = []): Notice
    {
        return new class($this->serviceConnectorMock, $this->registrationMock, $body) extends Notice
        {
            public static function requiredClaims(): array
            {
                return [Claim::VERSION, Claim::DEPLOYMENT_ID, Claim::NOTICE];
            }
        };
    }

    public function test_sub_returns_sub_from_body()
    {
        $sub = 'user-123';
        $body = ['sub' => $sub, 'iss' => 'https://example.com'];
        $notice = $this->createTestNotice($body);

        $this->assertEquals($sub, $notice->sub());
    }

    public function test_sub_returns_null_when_sub_missing()
    {
        $body = ['iss' => 'https://example.com', 'aud' => 'client-id'];
        $notice = $this->createTestNotice($body);

        $this->assertNull($notice->sub());
    }
}
