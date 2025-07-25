<?php

namespace Packback\Lti1p3\Claims;

/**
 * ContentItems Claim
 *
 * Claim key: https://purl.imsglobal.org/spec/lti-dl/claim/content_items
 *
 * Example payload:
 * {
 *     "https://purl.imsglobal.org/spec/lti-dl/claim/content_items": [
 *         {
 *             "type": "file",
 *             "title": "A file like a PDF that is my assignment submissions",
 *             "url": "https://my.example.com/assignment1.pdf",
 *             "mediaType": "application/pdf",
 *             "expiresAt": "2018-03-06T20:05:02Z"
 *         },
 *         {
 *             "type": "https://www.example.com/custom_type",
 *             "data": "somedata"
 *         }
 *     ],
 * }
 */
class ContentItems extends Claim
{
    public static function claimKey(): string
    {
        return Claim::DL_CONTENT_ITEMS;
    }
}
