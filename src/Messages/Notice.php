<?php

namespace Packback\Lti1p3\Messages;

use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\LtiException;
use Packback\Lti1p3\MessageValidators\NoticeMessageValidator;

class Notice extends LtiMessage
{
    protected array $message;

    public function __construct(
        protected IDatabase $db,
        protected ILtiServiceConnector $serviceConnector
    ) {}

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     */
    public static function new(
        IDatabase $db,
        ILtiServiceConnector $serviceConnector
    ): self {
        return new Notice($db, $serviceConnector);
    }

    public function initialize(array $message): static
    {
        return $this->setMessage($message)
            ->validate();
    }

    /**
     * Validates all aspects of an incoming LTI message launch and caches the launch if successful.
     *
     * @throws LtiException Will throw an LtiException if validation fails
     */
    public function validate(): static
    {
        return $this->validateMessage();
    }

    protected function messageValidator(): string
    {
        return NoticeMessageValidator::class;
    }

    protected function hasJwtToken(): bool
    {
        return isset($this->message['jwt']);
    }

    protected function getJwtToken(): string
    {
        return $this->message['jwt'];
    }
}
