<?php

namespace App\Service\User;

use App\Model\User;
use App\Repository\UserRepository;
use App\Service\BaseService;

/**
 * User Authentication Adapter
 */
class UserAuthenticationService extends BaseService
{
    /**
     * User Repository
     *
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Username
     *
     * @var string
     */
    protected $username;

    /**
     * Password
     *
     * @var string
     */
    protected $password;

    /**
     * Token
     *
     * @var Token
     */
    protected $token;

    /**
     * Constructor.
     *
     * @param UserRepository $userRepository
     * @param Token $token
     * @param string $username
     * @param string $password
     */
    public function __construct(UserRepository $userRepository, Token $token, $username, $password)
    {
        $this->userRepository = $userRepository;
        $this->token = $token;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Performs an authentication attempt.
     *
     * @return AuthenticationResult
     */
    public function authenticate()
    {
        $query = $this->userRepository->newQuery()->select('*')->where([
            'username' => $this->username,
            'disabled' => 0
        ]);

        $userRow = $query->execute()->fetch('assoc');

        if (empty($userRow)) {
            return new AuthenticationResult(AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND);
        }

        $user = new User($userRow);

        if (!$this->token->verifyHash($this->password, $user->password)) {
            return new AuthenticationResult(AuthenticationResult::FAILURE_CREDENTIAL_INVALID);
        }

        return new AuthenticationResult(AuthenticationResult::SUCCESS, $user);
    }
}
