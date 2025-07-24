<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\LaunchPresentation;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class LaunchPresentationTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_launch_presentation_constant()
    {
        $this->assertEquals(Claim::LAUNCH_PRESENTATION, LaunchPresentation::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = [
            'document_target' => 'iframe',
            'return_url' => 'https://example.com/return',
            'width' => 800,
            'height' => 600,
        ];
        $launchPresentation = new LaunchPresentation($body);

        $this->assertEquals($body, $launchPresentation->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $launchPresentationData = [
            'document_target' => 'window',
            'locale' => 'en-US',
        ];
        $messageBody = [Claim::LAUNCH_PRESENTATION => $launchPresentationData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $launchPresentation = LaunchPresentation::create($this->messageMock);

        $this->assertInstanceOf(LaunchPresentation::class, $launchPresentation);
        $this->assertEquals($launchPresentationData, $launchPresentation->getBody());
    }

    public function test_return_url_method_returns_return_url_from_body()
    {
        $returnUrl = 'https://example.com/return-here';
        $body = [
            'document_target' => 'iframe',
            'return_url' => $returnUrl,
        ];
        $launchPresentation = new LaunchPresentation($body);

        $this->assertEquals($returnUrl, $launchPresentation->returnUrl());
    }

    public function test_document_target_method_returns_document_target_from_body()
    {
        $documentTarget = 'window';
        $body = ['document_target' => $documentTarget, 'height' => 360, 'width' => 480];
        $launchPresentation = new LaunchPresentation($body);

        $this->assertEquals($documentTarget, $launchPresentation->documentTarget());
    }

    public function test_height_method_returns_height_from_body()
    {
        $height = 360;
        $body = ['document_target' => 'window', 'height' => $height, 'width' => 480];
        $launchPresentation = new LaunchPresentation($body);

        $this->assertEquals($height, $launchPresentation->height());
    }

    public function test_width_method_returns_width_from_body()
    {
        $width = 480;
        $body = ['document_target' => 'window', 'height' => 360, 'width' => $width];
        $launchPresentation = new LaunchPresentation($body);

        $this->assertEquals($width, $launchPresentation->width());
    }
}
