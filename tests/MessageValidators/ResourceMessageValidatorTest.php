<?php

namespace Tests\MessageValidators;

use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\MessageValidators\ResourceMessageValidator;
use Tests\TestCase;

class ResourceMessageValidatorTest extends TestCase
{
    private static function validJwtBody()
    {
        return [
            'sub' => 'subscriber',
            LtiConstants::MESSAGE_TYPE => ResourceMessageValidator::getMessageType(),
            LtiConstants::VERSION => LtiConstants::V1_3,
            LtiConstants::ROLES => [],
            LtiConstants::RESOURCE_LINK => [
                'id' => 'unique-id',
            ],
        ];
    }
    public function test_it_can_validate()
    {
        $this->assertTrue(ResourceMessageValidator::canValidate(self::validJwtBody()));
    }

    public function test_it_cannot_validate()
    {
        $jwtBody = self::validJwtBody();
        $jwtBody[LtiConstants::MESSAGE_TYPE] = 'some other type';

        $this->assertFalse(ResourceMessageValidator::canValidate($jwtBody));
    }

    public function test_jwt_body_is_valid()
    {
        $this->assertNull(ResourceMessageValidator::validate(self::validJwtBody()));
    }

    public function test_jwt_body_is_invalid_missing_sub()
    {
        $jwtBody = self::validJwtBody();
        $jwtBody['sub'] = '';

        $this->expectException(LtiException::class);

        ResourceMessageValidator::validate($jwtBody);
    }

    public function test_jwt_body_is_invalid_missing_lti_version()
    {
        $jwtBody = self::validJwtBody();
        unset($jwtBody[LtiConstants::VERSION]);

        $this->expectException(LtiException::class);

        ResourceMessageValidator::validate($jwtBody);
    }

    public function test_jwt_body_is_invalid_wrong_lti_version()
    {
        $jwtBody = self::validJwtBody();
        $jwtBody[LtiConstants::VERSION] = '1.2.0';

        $this->expectException(LtiException::class);

        ResourceMessageValidator::validate($jwtBody);
    }

    public function test_jwt_body_is_invalid_missing_roles()
    {
        $jwtBody = self::validJwtBody();
        unset($jwtBody[LtiConstants::ROLES]);

        $this->expectException(LtiException::class);

        ResourceMessageValidator::validate($jwtBody);
    }

    public function test_jwt_body_is_invalid_missing_resource_link_id()
    {
        $jwtBody = self::validJwtBody();
        unset($jwtBody[LtiConstants::RESOURCE_LINK]['id']);

        $this->expectException(LtiException::class);

        ResourceMessageValidator::validate($jwtBody);
    }
}
