<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\DeepLinkSettings;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class DeepLinkSettingsTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_deep_link_settings_constant()
    {
        $this->assertEquals(Claim::DL_DEEP_LINK_SETTINGS, DeepLinkSettings::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = ['deep_link_return_url' => 'https://example.com/return', 'accept_types' => ['link']];
        $deepLinkSettings = new DeepLinkSettings($body);

        $this->assertEquals($body, $deepLinkSettings->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $deepLinkSettingsData = ['accept_presentation_document_targets' => ['iframe', 'window']];
        $messageBody = [Claim::DL_DEEP_LINK_SETTINGS => $deepLinkSettingsData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $deepLinkSettings = DeepLinkSettings::create($this->messageMock);

        $this->assertInstanceOf(DeepLinkSettings::class, $deepLinkSettings);
        $this->assertEquals($deepLinkSettingsData, $deepLinkSettings->getBody());
    }

    public function test_accept_types_method_returns_accept_types_from_body()
    {
        $acceptTypes = ['link', 'file', 'html', 'ltiResourceLink'];
        $body = ['accept_types' => $acceptTypes];
        $deepLinkSettings = new DeepLinkSettings($body);

        $this->assertEquals($acceptTypes, $deepLinkSettings->acceptTypes());
    }

    public function test_can_accept_type_method_returns_true_for_valid_type()
    {
        $acceptTypes = ['link', 'file'];
        $body = ['accept_types' => $acceptTypes];
        $deepLinkSettings = new DeepLinkSettings($body);

        $this->assertTrue($deepLinkSettings->canAcceptType('link'));
        $this->assertFalse($deepLinkSettings->canAcceptType('html'));
    }

    public function test_accept_media_types_method_returns_media_types_from_body()
    {
        $mediaTypes = 'image/*,text/html,application/json';
        $body = ['accept_media_types' => $mediaTypes];
        $deepLinkSettings = new DeepLinkSettings($body);

        $this->assertEquals($mediaTypes, $deepLinkSettings->acceptMediaTypes());
    }

    public function test_accept_presentation_document_targets_method_returns_targets_from_body()
    {
        $targets = ['iframe', 'window'];
        $body = ['accept_presentation_document_targets' => $targets];
        $deepLinkSettings = new DeepLinkSettings($body);

        $this->assertEquals($targets, $deepLinkSettings->acceptPresentationDocumentTargets());
    }

    public function test_can_accept_presentation_document_target_method_returns_true_for_valid_target()
    {
        $targets = ['iframe', 'window'];
        $body = ['accept_presentation_document_targets' => $targets];
        $deepLinkSettings = new DeepLinkSettings($body);

        $this->assertTrue($deepLinkSettings->canAcceptPresentationDocumentTarget('iframe'));
        $this->assertFalse($deepLinkSettings->canAcceptPresentationDocumentTarget('popup'));
    }

    public function test_accept_lineitem_method_returns_lineitem_from_body()
    {
        $body = ['accept_lineitem' => true];
        $deepLinkSettings = new DeepLinkSettings($body);

        $this->assertTrue($deepLinkSettings->acceptLineitem());
    }

    public function test_accept_multiple_method_returns_multiple_from_body()
    {
        $body = ['accept_multiple' => true];
        $deepLinkSettings = new DeepLinkSettings($body);

        $this->assertTrue($deepLinkSettings->acceptMultiple());
    }

    public function test_auto_create_method_returns_auto_create_from_body()
    {
        $body = ['auto_create' => true];
        $deepLinkSettings = new DeepLinkSettings($body);

        $this->assertTrue($deepLinkSettings->autoCreate());
    }

    public function test_title_method_returns_title_from_body()
    {
        $title = 'Certification Deep Linking';
        $body = ['title' => $title];
        $deepLinkSettings = new DeepLinkSettings($body);

        $this->assertEquals($title, $deepLinkSettings->title());
    }

    public function test_text_method_returns_text_from_body()
    {
        $text = 'Certification Default Text Description';
        $body = ['text' => $text];
        $deepLinkSettings = new DeepLinkSettings($body);

        $this->assertEquals($text, $deepLinkSettings->text());
    }

    public function test_data_method_returns_data_from_body()
    {
        $data = '909708';
        $body = ['data' => $data];
        $deepLinkSettings = new DeepLinkSettings($body);

        $this->assertEquals($data, $deepLinkSettings->data());
    }

    public function test_deep_link_return_url_method_returns_return_url_from_body()
    {
        $returnUrl = 'https://ltiadvantagevalidator.imsglobal.org/ltitool/deeplinkresponse.html';
        $body = ['deep_link_return_url' => $returnUrl];
        $deepLinkSettings = new DeepLinkSettings($body);

        $this->assertEquals($returnUrl, $deepLinkSettings->deepLinkReturnUrl());
    }
}
