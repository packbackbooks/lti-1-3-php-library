<?php

namespace Tests\MessageValidators;

use Packback\Lti1p3\Claims\Claim;
use Packback\Lti1p3\LtiConstants;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\MessageValidators\NoticeMessageValidator;
use Tests\TestCase;

class NoticeMessageValidatorTest extends TestCase
{
    public function test_it_validates_valid_notice_message()
    {
        $validJwtBody = [
            Claim::VERSION => LtiConstants::V1_3,
            'iss' => 'https://test.issuer.com',
            'aud' => 'test-client-id',
            'nonce' => 'test-nonce',
            Claim::DEPLOYMENT_ID => 'test-deployment',
            Claim::NOTICE => [
                'notice_type' => LtiConstants::NOTICE_TYPE_HELLOWORLD,
                'timestamp' => '2024-01-15T10:30:00Z',
                'data' => ['message' => 'Hello, World!'],
            ],
        ];

        $this->assertNull(NoticeMessageValidator::validate($validJwtBody));
    }

    public function test_it_throws_exception_for_missing_lti_version()
    {
        $jwtBodyWithoutVersion = [
            'iss' => 'https://test.issuer.com',
            'aud' => 'test-client-id',
            'nonce' => 'test-nonce',
            Claim::DEPLOYMENT_ID => 'test-deployment',
        ];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage('Missing LTI Version');

        NoticeMessageValidator::validate($jwtBodyWithoutVersion);
    }

    public function test_it_throws_exception_for_incorrect_lti_version()
    {
        $jwtBodyWithWrongVersion = [
            Claim::VERSION => '1.2.0',
            'iss' => 'https://test.issuer.com',
            'aud' => 'test-client-id',
            'nonce' => 'test-nonce',
            Claim::DEPLOYMENT_ID => 'test-deployment',
        ];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage('Incorrect version, expected 1.3.0');

        NoticeMessageValidator::validate($jwtBodyWithWrongVersion);
    }

    public function test_it_accepts_correct_lti_version()
    {
        $jwtBodyWithCorrectVersion = [
            Claim::VERSION => LtiConstants::V1_3,
            'iss' => 'https://test.issuer.com',
            'aud' => 'test-client-id',
            'nonce' => 'test-nonce',
            Claim::DEPLOYMENT_ID => 'test-deployment',
        ];

        $this->assertNull(NoticeMessageValidator::validate($jwtBodyWithCorrectVersion));
    }

    public function test_it_validates_minimal_notice_message()
    {
        $minimalJwtBody = [
            Claim::VERSION => LtiConstants::V1_3,
        ];

        $this->assertNull(NoticeMessageValidator::validate($minimalJwtBody));
    }

    public function test_it_handles_empty_jwt_body()
    {
        $emptyJwtBody = [];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage('Missing LTI Version');

        NoticeMessageValidator::validate($emptyJwtBody);
    }

    public function test_it_handles_null_version()
    {
        $jwtBodyWithNullVersion = [
            Claim::VERSION => null,
            'iss' => 'https://test.issuer.com',
        ];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage('Missing LTI Version');

        NoticeMessageValidator::validate($jwtBodyWithNullVersion);
    }

    public function test_it_handles_empty_string_version()
    {
        $jwtBodyWithEmptyVersion = [
            Claim::VERSION => '',
            'iss' => 'https://test.issuer.com',
        ];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage('Incorrect version, expected 1.3.0');

        NoticeMessageValidator::validate($jwtBodyWithEmptyVersion);
    }

    public function test_it_handles_numeric_version()
    {
        $jwtBodyWithNumericVersion = [
            Claim::VERSION => 1.3,
            'iss' => 'https://test.issuer.com',
        ];

        $this->expectException(LtiException::class);
        $this->expectExceptionMessage('Incorrect version, expected 1.3.0');

        NoticeMessageValidator::validate($jwtBodyWithNumericVersion);
    }

    public function test_it_validates_notice_with_all_optional_claims()
    {
        $fullJwtBody = [
            Claim::VERSION => LtiConstants::V1_3,
            'iss' => 'https://test.issuer.com',
            'aud' => 'test-client-id',
            'sub' => 'test-user-id',
            'nonce' => 'test-nonce',
            'iat' => time(),
            'exp' => time() + 3600,
            Claim::DEPLOYMENT_ID => 'test-deployment',
            Claim::TARGET_LINK_URI => 'https://test.tool.com/launch',
            Claim::ROLES => [LtiConstants::INSTITUTION_INSTRUCTOR],
            Claim::CONTEXT => [
                'id' => 'test-context-id',
                'label' => 'Test Course',
                'title' => 'Test Course Title',
            ],
            Claim::NOTICE => [
                'notice_type' => LtiConstants::NOTICE_TYPE_CONTEXTCOPY,
                'timestamp' => '2024-01-15T10:30:00Z',
                'data' => [
                    'source_context_id' => 'source-123',
                    'target_context_id' => 'target-456',
                ],
            ],
        ];

        $this->assertNull(NoticeMessageValidator::validate($fullJwtBody));
    }

    public function test_version_constant_is_correct()
    {
        $this->assertEquals('1.3.0', LtiConstants::V1_3);
    }

    public function test_pns_claim_constants_are_defined()
    {
        $this->assertEquals('https://purl.imsglobal.org/spec/lti/claim/platformnotificationservice', Claim::PLATFORMNOTIFICATIONSERVICE);
        $this->assertEquals('https://purl.imsglobal.org/spec/lti/claim/notice', Claim::NOTICE);
    }

    public function test_notice_type_constants_are_defined()
    {
        $this->assertEquals('LtiHelloWorldNotice', LtiConstants::NOTICE_TYPE_HELLOWORLD);
        $this->assertEquals('LtiContextCopyNotice', LtiConstants::NOTICE_TYPE_CONTEXTCOPY);
        $this->assertEquals('LtiAssetProcessorSubmissionNotice', LtiConstants::NOTICE_TYPE_ASSETPROCESSORSUBMISSION);
    }

    public function test_it_validates_with_different_notice_types()
    {
        $noticeTypes = [
            LtiConstants::NOTICE_TYPE_HELLOWORLD,
            LtiConstants::NOTICE_TYPE_CONTEXTCOPY,
            LtiConstants::NOTICE_TYPE_ASSETPROCESSORSUBMISSION,
        ];

        foreach ($noticeTypes as $noticeType) {
            $jwtBody = [
                Claim::VERSION => LtiConstants::V1_3,
                Claim::NOTICE => [
                    'notice_type' => $noticeType,
                    'timestamp' => '2024-01-15T10:30:00Z',
                ],
            ];

            $this->assertNull(NoticeMessageValidator::validate($jwtBody));
        }
    }
}
