<?php

namespace App\Service\User;

use App\DataRow\UserRow;
use App\DataMapper\UserMapper;
use Odan\Slim\Session\Session;
use RuntimeException;

/**
 * Authentication
 */
class AuthenticationService
{

    /**
     * Session
     *
     * @var Session
     */
    private $session;

    /**
     * @var Token
     */
    private $token;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var UserMapper
     */
    private $userModel;

    /**
     * UserSession constructor.
     *
     * @param Session $session Storage
     * @param UserMapper $userModel The User model
     * @param string $secret
     */
    public function __construct(Session $session, UserMapper $userModel, string $secret)
    {
        $this->session = $session;
        $this->userModel = $userModel;
        $this->secret = $secret;
        $this->token = $this->createToken($secret);
    }

    /**
     * Returns true if and only if an identity is available from storage
     *
     * @return bool
     */
    public function hasIdentity()
    {
        return !empty($this->session->get('user'));
    }

    /**
     * Set the identity into storage or null if no identity is available
     *
     * @param UserRow $user
     * @return void
     */
    public function setIdentity(UserRow $user)
    {
        $this->session->set('user', $user);
    }

    /**
     * Returns the identity from storage or null if no identity is available
     *
     * @return UserRow
     */
    public function getIdentity()
    {
        $user = $this->session->get('user');
        if (!$user) {
            throw new RuntimeException('No identity available');
        }

        return $user;
    }

    /**
     * Clears the identity from persistent storage
     *
     * @return void
     */
    public function clearIdentity()
    {
        $this->session->remove('user');

        // Clears all session data and regenerates session ID
        if ($this->session->isStarted()) {
            //$this->session->regenerateId();
            $this->session->destroy();
        }
    }

    /**
     * Create token object.
     *
     * @param string $secret
     * @return Token
     */
    private function createToken($secret): Token
    {
        return new Token($this->session->getId() . $secret);
    }

    /**
     * Get user Id.
     *
     * @return string User Id
     */
    public function getId(): string
    {
        $result = (string)$this->getIdentity()->id;

        if (empty($result)) {
            throw new RuntimeException(__('Invalid or empty User-ID'));
        }

        return $result;
    }

    /**
     * Performs an authentication attempt.
     *
     * @param string $username
     * @param string $password
     * @return AuthenticationResult
     */
    public function authenticate($username, $password): AuthenticationResult
    {
        // Check username and password
        $authResult = $this->loginUser($username, $password);

        if (!$authResult->isValid() || !$user = $authResult->getIdentity()) {
            return $authResult;
        }

        // Clear session data
        $this->session->destroy();
        $this->session->start();

        // Create new session id
        $this->session->regenerateId();

        // Create new token
        $this->token = $this->createToken($this->secret);

        // Store user settings in session
        $this->setIdentity($user);

        return $authResult;
    }

    /**
     * Login user with username and password
     *
     * @param string $username Username
     * @param string $password Password
     * @return AuthenticationResult Status
     */
    private function loginUser(string $username, string $password): AuthenticationResult
    {
        $user = $this->userModel->findByUsername($username);

        if (empty($user)) {
            return new AuthenticationResult(AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND);
        }

        if (!$this->token->verify($password, $user->password)) {
            return new AuthenticationResult(AuthenticationResult::FAILURE_CREDENTIAL_INVALID);
        }

        return new AuthenticationResult(AuthenticationResult::SUCCESS, $user);
    }

    /**
     * Check user permission.
     *
     * @param string|array $role (e.g. 'ROLE_ADMIN' or 'ROLE_USER')
     * or array('ROLE_ADMIN', 'ROLE_USER')
     * @return bool Status
     */
    public function hasRole($role): bool
    {
        // Current user role
        $userRole = $this->getIdentity()->role;

        // Full access for admin
        if ($userRole === Role::ROLE_ADMIN) {
            return true;
        }
        if ($role === $userRole) {
            return true;
        }
        if (is_array($role) && in_array($userRole, $role)) {
            return true;
        }

        return false;
    }
}
