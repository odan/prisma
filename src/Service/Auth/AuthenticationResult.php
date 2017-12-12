<?php

namespace App\Service\Auth;

use App\Entity\User;

/**
 * Class AuthenticationResult
 */
class AuthenticationResult
{
    const SUCCESS = 1;
    const FAILURE = 0;
    const FAILURE_IDENTITY_NOT_FOUND = -1;
    const FAILURE_IDENTITY_AMBIGUOUS = -2;
    const FAILURE_CREDENTIAL_INVALID = -3;
    const FAILURE_UNCATEGORIZED = -4;

    /**
     * Code
     *
     * @var int
     */
    private $code;

    /**
     * Identity
     *
     * @var User|null
     */
    private $identity;

    /**
     * Messages
     *
     * @var array
     */
    private $messages;

    /**
     * Constructor.
     *
     * @param int $code Code
     * @param User $identity User
     * @param array $messages Messages
     */
    public function __construct($code, User $identity = null, $messages = [])
    {
        $this->code = $code;
        $this->identity = $identity;
        $this->messages = $messages;
    }

    /**
     * Returns TRUE if and only if the result represents a successful authentication attempt.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->code === static::SUCCESS;
    }

    /**
     * Returns the AuthenticationResult constant identifier associated
     * with the specific result. This may be used in situations where the developer
     * wishes to distinguish among several authentication result types.
     * This allows developers to maintain detailed authentication result statistics,
     * for example. Another use of this feature is to provide specific,
     * customized messages to users for usability reasons, though developers are
     * encouraged to consider the risks of providing such detailed reasons to users,
     * instead of a general authentication failure message.
     *
     * @return int Result constant identifier
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Returns the identity of the authentication attempt.
     *
     * @return User|null User
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * Returns an array of messages regarding a failed authentication attempt.
     *
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
