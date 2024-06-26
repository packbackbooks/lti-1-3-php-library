<?php

namespace Tests;

use Packback\Lti1p3\LtiDeployment;

class LtiDeploymentTest extends TestCase
{
    private $id = 'a deployment';
    private $deployment;
    public function setUp(): void
    {
        $this->deployment = new LtiDeployment($this->id);
    }

    public function testItInstantiates()
    {
        $this->assertInstanceOf(LtiDeployment::class, $this->deployment);
    }

    public function testCreatesANewInstance()
    {
        $deployment = LtiDeployment::new($this->id);

        $this->assertInstanceOf(LtiDeployment::class, $deployment);
    }

    public function testItGetsDeploymentId()
    {
        $result = $this->deployment->getDeploymentId();

        $this->assertEquals($this->id, $result);
    }

    public function testItSetsDeploymentId()
    {
        $expected = 'expected';

        $this->deployment->setDeploymentId($expected);

        $this->assertEquals($expected, $this->deployment->getDeploymentId());
    }
}
