<?php

namespace App\Service\User;

use App\Service\AbstractService;

/**
 * User Authentication Adapter
 */
class AuthenticationService extends AbstractService
{
    /**
     * User Repository
     *
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Username
     *
     * @var string
     */
    private $username;

    /**
     * Password
     *
     * @var string
     */
    private $password;

    /**
     * Token
     *
     * @var Token
     */
    private $token;

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
    public function authenticate(): AuthenticationResult
    {
        $user = $this->userRepository->findByUsername($this->username);

        if (empty($user)) {
            return new AuthenticationResult(AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND);
        }

        if (!$this->token->verifyHash($this->password, $user->password)) {
            return new AuthenticationResult(AuthenticationResult::FAILURE_CREDENTIAL_INVALID);
        }

        return new AuthenticationResult(AuthenticationResult::SUCCESS, $user);
    }
}
