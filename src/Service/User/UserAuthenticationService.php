<?php

namespace App\Service\User;

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
