<?php

namespace Tests;

use Mockery;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\LtiAssignmentsGradesService;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\LtiGrade;
use Packback\Lti1p3\LtiLineitem;

class LtiAssignmentsGradesServiceTest extends TestCase
{
    private $connector;
    private $registration;
    public function setUp(): void
    {
        $this->connector = Mockery::mock(ILtiServiceConnector::class);
        $this->registration = Mockery::mock(ILtiRegistration::class);
    }

    public function testItInstantiates()
    {
        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, []);

        $this->assertInstanceOf(LtiAssignmentsGradesService::class, $service);
    }

    public function testItGetsScope()
    {
        $serviceData = [
            'scope' => ['asdf'],
        ];

        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, $serviceData);

        $result = $service->getScope();

        $this->assertEquals($serviceData['scope'], $result);
    }

    public function testItGetsResourceLaunchLineItem()
    {
        $ltiLineitemData = [
            'id' => 'testId',
        ];

        $serviceData = [
            'lineitem' => $ltiLineitemData['id'],
        ];

        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, $serviceData);

        $expected = new LtiLineitem($ltiLineitemData);

        $result = $service->getResourceLaunchLineItem();

        $this->assertEquals($expected, $result);
    }

    public function testItGetsNullResourceLaunchLineItem()
    {
        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, []);

        $result = $service->getResourceLaunchLineItem();

        $this->assertNull($result);
    }

    public function testItGetsSingleLineItem()
    {
        $ltiLineitemData = [
            'id' => 'testId',
        ];

        $serviceData = [
            'scope' => [LtiConstants::AGS_SCOPE_LINEITEM],
        ];

        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, $serviceData);

        $response = [
            'body' => $ltiLineitemData,
        ];

        $this->connector->shouldReceive('makeServiceRequest')
            ->once()->andReturn($response);

        $expected = new LtiLineitem($ltiLineitemData);

        $result = $service->getLineItem('someUrl');

        $this->assertEquals($expected, $result);
    }

    public function testItGetsSingleLineItemWithReadonlyScope()
    {
        $ltiLineitemData = [
            'id' => 'testId',
        ];

        $serviceData = [
            'scope' => [LtiConstants::AGS_SCOPE_LINEITEM_READONLY],
        ];

        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, $serviceData);

        $response = [
            'body' => $ltiLineitemData,
        ];

        $this->connector->shouldReceive('makeServiceRequest')
            ->once()->andReturn($response);

        $expected = new LtiLineitem($ltiLineitemData);

        $result = $service->getLineItem('someUrl');

        $this->assertEquals($expected, $result);
    }

    public function testItPutsAGrade()
    {
        $serviceData = [
            'scope' => [LtiConstants::AGS_SCOPE_SCORE],
            'lineitems' => 'https://canvas.localhost/api/lti/courses/8/line_items',
        ];
        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, $serviceData);

        $lineItem = new LtiLineitem([
            'id' => 'https://canvas.localhost/api/lti/courses/8/line_items/29?foo=bar',
        ]);

        $expected = [
            'scoreGiven' => 10,
            'scoreMaximum' => 15,
        ];
        $grade = new LtiGrade($expected);
        $this->connector->shouldReceive('makeServiceRequest')
            ->once()->andReturn($expected);

        $result = $service->putGrade($grade, $lineItem);

        $this->assertEquals($expected, $result);
    }

    public function testItFindsALineItem()
    {
        $ltiLineitemData = [
            'id' => 'testId',
        ];

        $serviceData = [
            'scope' => [LtiConstants::AGS_SCOPE_LINEITEM],
            'lineitems' => 'https://canvas.localhost/api/lti/courses/8/line_items',
        ];

        $lineItem = new LtiLineitem($ltiLineitemData);
        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, $serviceData);

        $response = [$ltiLineitemData];

        $this->connector->shouldReceive('getAll')
            ->once()->andReturn($response);

        $result = $service->findLineItem($lineItem);

        $this->assertEquals($lineItem, $result);
    }

    public function testItFindsNoLineItem()
    {
        $ltiLineitemData = [
            'id' => 'testId',
        ];

        $serviceData = [
            'scope' => [LtiConstants::AGS_SCOPE_LINEITEM],
            'lineitems' => 'https://canvas.localhost/api/lti/courses/8/line_items',
        ];

        $lineItem = new LtiLineitem($ltiLineitemData);
        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, $serviceData);

        $this->connector->shouldReceive('getAll')
            ->once()->andReturn([]);

        $result = $service->findLineItem($lineItem);

        $this->assertNull($result);
    }

    public function testItUpdatesALineItem()
    {
        $serviceData = [
            'scope' => [LtiConstants::AGS_SCOPE_LINEITEM],
            'lineitems' => 'https://lms.example.com/context/2923/lineitems/',
        ];

        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, $serviceData);

        $ltiLineitemData = [
            'id' => 'https://lms.example.com/context/2923/lineitems/23',
        ];

        $response = [
            'body' => $ltiLineitemData,
        ];

        $this->connector->shouldReceive('makeServiceRequest')
            ->once()->andReturn($response);

        $expected = new LtiLineitem($ltiLineitemData);

        $result = $service->updateLineitem(new LtiLineitem($ltiLineitemData));

        $this->assertEquals($expected, $result);
    }

    public function testItCreatesALineItem()
    {
        $ltiLineitemData = [
            'id' => 'testId',
        ];

        $serviceData = [
            'scope' => [LtiConstants::AGS_SCOPE_LINEITEM],
            'lineitems' => 'https://canvas.localhost/api/lti/courses/8/line_items',
        ];

        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, $serviceData);

        $response = [
            'body' => $ltiLineitemData,
        ];

        $this->connector->shouldReceive('makeServiceRequest')
            ->once()->andReturn($response);

        $expected = new LtiLineitem($ltiLineitemData);

        $result = $service->createLineItem(new LtiLineItem);

        $this->assertEquals($expected, $result);
    }

    public function testItDeletesALineItem()
    {
        $serviceData = [
            'scope' => [LtiConstants::AGS_SCOPE_LINEITEM],
            'lineitem' => 'https://canvas.localhost/api/lti/courses/8/line_items/27',
        ];

        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, $serviceData);

        $response = [
            'status' => 204,
            'body' => null,
        ];

        $this->connector->shouldReceive('makeServiceRequest')
            ->once()->andReturn($response);

        $result = $service->deleteLineitem();

        $this->assertEquals($response, $result);
    }

    public function testItFindsOrCreatesALineItem()
    {
        $ltiLineitemData = [
            'id' => 'testId',
        ];

        $serviceData = [
            'scope' => [LtiConstants::AGS_SCOPE_LINEITEM],
            'lineitems' => 'https://canvas.localhost/api/lti/courses/8/line_items',
        ];

        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, $serviceData);

        $response = [
            'body' => $ltiLineitemData,
        ];

        // Find Line Item
        $this->connector->shouldReceive('getAll')
            ->once()->andReturn([]);
        // Create Line Item
        $this->connector->shouldReceive('makeServiceRequest')
            ->once()->andReturn($response);

        $expected = new LtiLineitem($ltiLineitemData);

        $result = $service->findOrCreateLineitem($expected);

        $this->assertEquals($expected, $result);
    }

    public function testItThrowsWithMissingScope()
    {
        $ltiLineitemData = [
            'id' => 'testId',
        ];

        $serviceData = [
            'scope' => [],
        ];

        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, $serviceData);

        $expected = new LtiLineitem($ltiLineitemData);

        $this->expectException(LtiException::class);

        $service->getLineItem('someUrl');
    }

    public function testItSetsServiceData()
    {
        $expected = ['foo' => 'bar'];

        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, []);
        $service->setServiceData($expected);

        $actual = $service->getServiceData();

        $this->assertEquals($expected, $actual);
    }

    public function testItGetsGrades()
    {
        $serviceData = [
            'scope' => [LtiConstants::AGS_SCOPE_LINEITEM],
            'lineitems' => 'https://canvas.localhost/api/lti/courses/8/line_items?foo=bar',
        ];
        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, $serviceData);

        $lineItem = new LtiLineitem([
            'id' => 'https://canvas.localhost/api/lti/courses/8/line_items/29',
        ]);

        $expected = [[
            'scoreGiven' => 10,
            'scoreMaximum' => 15,
        ]];
        $this->connector->shouldReceive('getAll')
            ->once()->andReturn($expected);

        $result = $service->getGrades($lineItem);

        $this->assertEquals($expected, $result);
    }

    public function testItGetsGradesWithLineItemInServiceData()
    {
        $serviceData = [
            'scope' => [LtiConstants::AGS_SCOPE_LINEITEM],
            'lineitem' => 'https://canvas.localhost/api/lti/courses/8/line_items/29',
        ];
        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, $serviceData);

        $expected = [[
            'scoreGiven' => 10,
            'scoreMaximum' => 15,
        ]];
        $this->connector->shouldReceive('getAll')
            ->once()->andReturn($expected);

        $result = $service->getGrades();

        $this->assertEquals($expected, $result);
    }

    public function testItGetsGradesWithoutLineItem()
    {
        $serviceData = [
            'scope' => [LtiConstants::AGS_SCOPE_LINEITEM],
            'lineitems' => 'https://canvas.localhost/api/lti/courses/8/line_items',
        ];
        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, $serviceData);

        $response = [
            'body' => [
                'id' => 'testId',
            ],
        ];
        // Create Line Item
        $this->connector->shouldReceive('makeServiceRequest')
            ->once()->andReturn($response);

        $expected = [[
            'scoreGiven' => 10,
            'scoreMaximum' => 15,
        ]];
        // Get Grades
        $this->connector->shouldReceive('getAll')
            ->once()->andReturn($expected);

        $result = $service->getGrades();

        $this->assertEquals($expected, $result);
    }

    public function testItGetsGradesWithEmptyLineItem()
    {
        $serviceData = [
            'scope' => [LtiConstants::AGS_SCOPE_LINEITEM],
            'lineitems' => 'https://canvas.localhost/api/lti/courses/8/line_items',
        ];
        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, $serviceData);

        $response = [[
            'id' => 'testId',
        ]];
        // Get Line Items
        $this->connector->shouldReceive('getAll')
            ->once()->andReturn($response);

        $expected = [[
            'scoreGiven' => 10,
            'scoreMaximum' => 15,
        ]];
        // Get Grades
        $this->connector->shouldReceive('getAll')
            ->once()->andReturn($expected);

        $result = $service->getGrades(new LtiLineitem);

        $this->assertEquals($expected, $result);
    }

    public function testItGetsLineItems()
    {
        $serviceData = [
            'scope' => [LtiConstants::AGS_SCOPE_LINEITEM],
            'lineitems' => 'https://canvas.localhost/api/lti/courses/8/line_items',
        ];

        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, $serviceData);

        $response = [
            'status' => 204,
            'body' => [],
        ];

        $this->connector->shouldReceive('getAll')
            ->once()->andReturn($response);

        $result = $service->getLineItems();

        $this->assertEquals($response, $result);
    }

    public function testItGetsALineItem()
    {
        $serviceData = [
            'scope' => [LtiConstants::AGS_SCOPE_LINEITEM],
            'lineitems' => 'https://canvas.localhost/api/lti/courses/8/line_items',
        ];

        $service = new LtiAssignmentsGradesService($this->connector, $this->registration, $serviceData);

        $response = [
            'status' => 204,
            'body' => ['id' => 'testId'],
        ];

        $expected = [
            'status' => 204,
            'body' => [['id' => 'testId']],
        ];

        $this->connector->shouldReceive('getAll')
            ->once()->andReturn($response);

        $result = $service->getLineItems();

        $this->assertEquals($expected, $result);
    }
}
