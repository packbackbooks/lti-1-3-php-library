<?php

namespace Tests;

use Packback\Lti1p3\LtiDeployment;

class LtiDeploymentTest extends TestCase
{
    protected function setUp(): void
    {
        $this->deployment = new LtiDeployment($this->id);
    }
    private $id = 'a deployment';
    private $deployment;

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(LtiDeployment::class, $this->deployment);
    }

    public function test_creates_a_new_instance()
    {
        $deployment = LtiDeployment::new($this->id);

        $this->assertInstanceOf(LtiDeployment::class, $deployment);
    }

    public function test_it_gets_deployment_id()
    {
        $result = $this->deployment->getDeploymentId();

        $this->assertEquals($this->id, $result);
    }

    public function test_it_sets_deployment_id()
    {
        $expected = 'expected';

        $this->deployment->setDeploymentId($expected);

        $this->assertEquals($expected, $this->deployment->getDeploymentId());
    }
}
