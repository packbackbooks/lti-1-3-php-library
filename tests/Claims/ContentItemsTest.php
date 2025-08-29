<?php

namespace Tests\Claims;

use Mockery;
use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\Claims\ContentItems;
use Packback\Lti1p3\Messages\LtiMessage;
use Tests\TestCase;

class ContentItemsTest extends TestCase
{
    private $messageMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messageMock = Mockery::mock(LtiMessage::class);
    }

    public function test_claim_key_returns_content_items_constant()
    {
        $this->assertEquals(Claim::DL_CONTENT_ITEMS, ContentItems::claimKey());
    }

    public function test_constructor_sets_body()
    {
        $body = [['type' => 'link', 'url' => 'https://example.com']];
        $contentItems = new ContentItems($body);

        $this->assertEquals($body, $contentItems->getBody());
    }

    public function test_create_method_creates_instance_from_message()
    {
        $contentItemsData = [['type' => 'file', 'url' => 'https://example.com/file.pdf']];
        $messageBody = [Claim::DL_CONTENT_ITEMS => $contentItemsData];
        $this->messageMock->shouldReceive('getBody')->andReturn($messageBody);

        $contentItems = ContentItems::create($this->messageMock);

        $this->assertInstanceOf(ContentItems::class, $contentItems);
        $this->assertEquals($contentItemsData, $contentItems->getBody());
    }
}
