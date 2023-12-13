<?php

namespace Tests;

use Packback\Lti1p3\LtiDeployment;

class LtiDeploymentTest extends TestCase
{
    private $name = 'a deployment';

    public function setUp(): void
    {
        $this->deployment = new LtiDeployment($this->name);
    }

    public function testItInstantiates()
    {
        $this->assertInstanceOf(LtiDeployment::class, $this->deployment);
    }

    public function testItGetsDeploymentId()
    {
        $result = $this->deployment->getDeploymentId();

        $this->assertEquals($this->name, $result);
    }

    public function testItSetsDeploymentId()
    {
        $expected = 'expected';

        $this->deployment->setDeploymentId($expected);

        $this->assertEquals($expected, $this->deployment->getDeploymentId());
    }
}
