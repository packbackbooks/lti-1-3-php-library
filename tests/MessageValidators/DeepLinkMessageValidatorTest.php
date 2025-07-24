<?php

namespace Tests\MessageValidators;

use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\MessageValidators\DeepLinkMessageValidator;
use Tests\TestCase;

class DeepLinkMessageValidatorTest extends TestCase
{
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
    public function test_it_can_validate()
    {
        $this->assertTrue(DeepLinkMessageValidator::canValidate(self::validJwtBody()));
    }

    public function test_it_cannot_validate()
    {
        $jwtBody = self::validJwtBody();
        $jwtBody[LtiConstants::MESSAGE_TYPE] = 'some other type';

        $this->assertFalse(DeepLinkMessageValidator::canValidate($jwtBody));
    }

    public function test_jwt_body_is_valid()
    {
        $this->assertNull(DeepLinkMessageValidator::validate(self::validJwtBody()));
    }

    public function test_jwt_body_is_invalid_missing_sub()
    {
        $jwtBody = self::validJwtBody();
        $jwtBody['sub'] = '';

        $this->expectException(LtiException::class);

        DeepLinkMessageValidator::validate($jwtBody);
    }

    public function test_jwt_body_is_invalid_missing_lti_version()
    {
        $jwtBody = self::validJwtBody();
        unset($jwtBody[LtiConstants::VERSION]);

        $this->expectException(LtiException::class);

        DeepLinkMessageValidator::validate($jwtBody);
    }

    public function test_jwt_body_is_invalid_wrong_lti_version()
    {
        $jwtBody = self::validJwtBody();
        $jwtBody[LtiConstants::VERSION] = '1.2.0';

        $this->expectException(LtiException::class);

        DeepLinkMessageValidator::validate($jwtBody);
    }

    public function test_jwt_body_is_invalid_missing_roles()
    {
        $jwtBody = self::validJwtBody();
        unset($jwtBody[LtiConstants::ROLES]);

        $this->expectException(LtiException::class);

        DeepLinkMessageValidator::validate($jwtBody);
    }

    public function test_jwt_body_is_invalid_missing_deep_link_setting()
    {
        $jwtBody = self::validJwtBody();
        unset($jwtBody[LtiConstants::DL_DEEP_LINK_SETTINGS]);

        $this->expectException(LtiException::class);

        DeepLinkMessageValidator::validate($jwtBody);
    }

    public function test_jwt_body_is_invalid_missing_deep_link_return_url()
    {
        $jwtBody = self::validJwtBody();
        unset($jwtBody[LtiConstants::DL_DEEP_LINK_SETTINGS]['deep_link_return_url']);

        $this->expectException(LtiException::class);

        DeepLinkMessageValidator::validate($jwtBody);
    }

    public function test_jwt_body_is_invalid_missing_accept_type()
    {
        $jwtBody = self::validJwtBody();
        unset($jwtBody[LtiConstants::DL_DEEP_LINK_SETTINGS]['accept_types']);

        $this->expectException(LtiException::class);

        DeepLinkMessageValidator::validate($jwtBody);
    }

    public function test_jwt_body_is_invalid_accept_type_is_invalid()
    {
        $jwtBody = self::validJwtBody();
        $jwtBody[LtiConstants::DL_DEEP_LINK_SETTINGS]['accept_types'] = [];

        $this->expectException(LtiException::class);

        DeepLinkMessageValidator::validate($jwtBody);
    }

    public function test_jwt_body_is_invalid_missing_presentation()
    {
        $jwtBody = self::validJwtBody();
        unset($jwtBody[LtiConstants::DL_DEEP_LINK_SETTINGS]['accept_presentation_document_targets']);

        $this->expectException(LtiException::class);

        DeepLinkMessageValidator::validate($jwtBody);
    }
}
