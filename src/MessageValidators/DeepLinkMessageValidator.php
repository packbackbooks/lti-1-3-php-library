<?php

namespace Packback\Lti1p3\MessageValidators;

use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;

class DeepLinkMessageValidator extends AbstractMessageValidator
{
    protected static $acceptTypes = ['ltiResourceLink', 'ltiAssetProcessor'];

    public static function getMessageType(): string
    {
        return LtiConstants::MESSAGE_TYPE_DEEPLINK;
    }

    /**
     * @throws LtiException
     */
    public static function validate(array $jwtBody): void
    {
        static::validateGenericMessage($jwtBody);

        if (empty($jwtBody[LtiConstants::DL_DEEP_LINK_SETTINGS])) {
            throw new LtiException('Missing Deep Linking Settings');
        }
        $deep_link_settings = $jwtBody[LtiConstants::DL_DEEP_LINK_SETTINGS];
        if (empty($deep_link_settings['deep_link_return_url'])) {
            throw new LtiException('Missing Deep Linking Return URL');
        }
        if (empty($deep_link_settings['accept_types']) || !empty(array_intersect(static::$acceptTypes, $deep_link_settings['accept_types']))) {
            throw new LtiException('Unsupported placement type');
        }
        if (empty($deep_link_settings['accept_presentation_document_targets'])) {
            throw new LtiException('Must support a presentation type');
        }
    }
}
