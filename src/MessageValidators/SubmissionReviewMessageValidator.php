<?php
namespace Packback\Lti1p3\MessageValidators;

use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\Interfaces\MessageValidator;

class SubmissionReviewMessageValidator implements MessageValidator
{
    public function canValidate(array $jwt_body)
    {
        return $jwt_body[LtiConstants::MESSAGE_TYPE] === 'LtiSubmissionReviewRequest';
    }

    public function validate(array $jwt_body)
    {
        if (empty($jwt_body['sub'])) {
            throw new LtiException('Must have a user (sub)');
        }
        if ($jwt_body[LtiConstants::VERSION] !== LtiConstants::V1_3) {
            throw new LtiException('Incorrect version, expected 1.3.0');
        }
        if (!isset($jwt_body[LtiConstants::ROLES])) {
            throw new LtiException('Missing Roles Claim');
        }
        if (empty($jwt_body[LtiConstants::RESOURCE_LINK]['id'])) {
            throw new LtiException('Missing Resource Link Id');
        }
        if (empty($jwt_body[LtiConstants::FOR_USER])) {
            throw new LtiException('Missing For User');
        }

        return true;
    }
}