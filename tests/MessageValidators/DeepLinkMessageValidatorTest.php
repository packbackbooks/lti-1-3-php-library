<?php

namespace Tests\MessageValidators;

use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\MessageValidators\DeepLinkMessageValidator;
use Tests\TestCase;

class DeepLinkMessageValidatorTest extends TestCase
{
    public function testItCanValidate()
    {
        $this->assertTrue(DeepLinkMessageValidator::canValidate(self::validJwtBody()));
    }

    public function testItCannotValidate()
    {
        $jwtBody = self::validJwtBody();
        $jwtBody[LtiConstants::MESSAGE_TYPE] = 'some other type';

        $this->assertFalse(DeepLinkMessageValidator::canValidate($jwtBody));
    }

    public function testJwtBodyIsValid()
    {
        $this->assertNull(DeepLinkMessageValidator::validate(self::validJwtBody()));
    }

    public function testJwtBodyIsInvalidMissingSub()
    {
        $jwtBody = self::validJwtBody();
        $jwtBody['sub'] = '';

        $this->expectException(LtiException::class);

        DeepLinkMessageValidator::validate($jwtBody);
    }

    public function testJwtBodyIsInvalidMissingLtiVersion()
    {
        $jwtBody = self::validJwtBody();
        unset($jwtBody[LtiConstants::VERSION]);

        $this->expectException(LtiException::class);

        DeepLinkMessageValidator::validate($jwtBody);
    }

    public function testJwtBodyIsInvalidWrongLtiVersion()
    {
        $jwtBody = self::validJwtBody();
        $jwtBody[LtiConstants::VERSION] = '1.2.0';

        $this->expectException(LtiException::class);

        DeepLinkMessageValidator::validate($jwtBody);
    }

    public function testJwtBodyIsInvalidMissingRoles()
    {
        $jwtBody = self::validJwtBody();
        unset($jwtBody[LtiConstants::ROLES]);

        $this->expectException(LtiException::class);

        DeepLinkMessageValidator::validate($jwtBody);
    }

    public function testJwtBodyIsInvalidMissingDeepLinkSetting()
    {
        $jwtBody = self::validJwtBody();
        unset($jwtBody[LtiConstants::DL_DEEP_LINK_SETTINGS]);

        $this->expectException(LtiException::class);

        DeepLinkMessageValidator::validate($jwtBody);
    }

    public function testJwtBodyIsInvalidMissingDeepLinkReturnUrl()
    {
        $jwtBody = self::validJwtBody();
        unset($jwtBody[LtiConstants::DL_DEEP_LINK_SETTINGS]['deep_link_return_url']);

        $this->expectException(LtiException::class);

        DeepLinkMessageValidator::validate($jwtBody);
    }

    public function testJwtBodyIsInvalidMissingAcceptType()
    {
        $jwtBody = self::validJwtBody();
        unset($jwtBody[LtiConstants::DL_DEEP_LINK_SETTINGS]['accept_types']);

        $this->expectException(LtiException::class);

        DeepLinkMessageValidator::validate($jwtBody);
    }

    public function testJwtBodyIsInvalidAcceptTypeIsInvalid()
    {
        $jwtBody = self::validJwtBody();
        $jwtBody[LtiConstants::DL_DEEP_LINK_SETTINGS]['accept_types'] = [];

        $this->expectException(LtiException::class);

        DeepLinkMessageValidator::validate($jwtBody);
    }

    public function testJwtBodyIsInvalidMissingPresentation()
    {
        $jwtBody = self::validJwtBody();
        unset($jwtBody[LtiConstants::DL_DEEP_LINK_SETTINGS]['accept_presentation_document_targets']);

        $this->expectException(LtiException::class);

        DeepLinkMessageValidator::validate($jwtBody);
    }

    private static function validJwtBody()
    {
        return [
            'sub' => 'subscriber',
            LtiConstants::MESSAGE_TYPE => DeepLinkMessageValidator::getMessageType(),
            LtiConstants::VERSION => LtiConstants::V1_3,
            LtiConstants::ROLES => [],
            LtiConstants::DL_DEEP_LINK_SETTINGS => [
                'deep_link_return_url' => 'https://example.com',
                'accept_types' => ['ltiResourceLink'],
                'accept_presentation_document_targets' => ['iframe'],
            ],
        ];
    }
}
