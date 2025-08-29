<?php

namespace Tests\Messages;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\Context;
use Packback\Lti1p3\Claims\OriginContexts;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Messages\ContextCopyNotice;
use Tests\TestCase;

class ContextCopyNoticeTest extends TestCase
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
            Context::claimKey(),
            OriginContexts::claimKey(),
        ];

        $this->assertEquals($expectedClaims, ContextCopyNotice::requiredClaims());
    }

    public function test_context_claim_returns_context_instance()
    {
        $context = ['id' => 'context-123', 'label' => 'Test Course'];
        $body = [Claim::CONTEXT => $context];
        $message = new ContextCopyNotice($this->serviceConnectorMock, $this->registrationMock, $body);

        $contextClaim = $message->contextClaim();

        $this->assertInstanceOf(Context::class, $contextClaim);
        $this->assertEquals($context, $contextClaim->getBody());
    }

    public function test_origin_contexts_claim_returns_origin_contexts_instance()
    {
        $originContexts = ['contexts' => [['id' => 'original-context-456']]];
        $body = [Claim::ORIGIN_CONTEXTS => $originContexts];
        $message = new ContextCopyNotice($this->serviceConnectorMock, $this->registrationMock, $body);

        $originContextsClaim = $message->originContextsClaim();

        $this->assertInstanceOf(OriginContexts::class, $originContextsClaim);
        $this->assertEquals($originContexts, $originContextsClaim->getBody());
    }

    public function test_sub_method_returns_notice_type_from_body()
    {
        $body = ['sub' => 'LtiContextCopyNotice'];
        $message = new ContextCopyNotice($this->serviceConnectorMock, $this->registrationMock, $body);

        $sub = $message->sub();

        $this->assertEquals('LtiContextCopyNotice', $sub);
    }
}
