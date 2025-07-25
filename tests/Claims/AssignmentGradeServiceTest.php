<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\AssignmentGradeService;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class AssignmentGradeServiceTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_assignment_grade_service_constant()
    {
        $this->assertEquals(Claim::AGS_ENDPOINT, AssignmentGradeService::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = [
            'lineitems' => 'https://example.com/lineitems',
            'scope' => ['https://purl.imsglobal.org/spec/lti-ags/scope/lineitem'],
        ];
        $ags = new AssignmentGradeService($body);

        $this->assertEquals($body, $ags->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $agsData = [
            'lineitem' => 'https://example.com/lineitem/123',
            'scope' => ['https://purl.imsglobal.org/spec/lti-ags/scope/score'],
        ];
        $messageBody = [Claim::AGS_ENDPOINT => $agsData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $ags = AssignmentGradeService::create($this->messageMock);

        $this->assertInstanceOf(AssignmentGradeService::class, $ags);
        $this->assertEquals($agsData, $ags->getBody());
    }

    public function test_lineitems_method_returns_lineitems_from_body()
    {
        $lineitems = 'https://example.com/lineitems';
        $body = ['lineitems' => $lineitems, 'scope' => ['read']];
        $ags = new AssignmentGradeService($body);

        $this->assertEquals($lineitems, $ags->lineitems());
    }

    public function test_scope_method_returns_scope_from_body()
    {
        $scope = [
            'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
            'https://purl.imsglobal.org/spec/lti-ags/scope/score',
        ];
        $body = ['lineitems' => 'https://example.com/lineitems', 'scope' => $scope];
        $ags = new AssignmentGradeService($body);

        $this->assertEquals($scope, $ags->scope());
    }
}
