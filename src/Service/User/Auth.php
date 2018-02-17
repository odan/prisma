<?php

namespace App\Service\User;

use App\Entity\UserEntity;
use App\Table\UserTable;
use Odan\Slim\Session\Session;
use RuntimeException;

/**
 * Authentication and authorisation
 */
class Auth
{

    /**
     * Session
     *
     * @var Session
     */
    private $session;

    /**
     * @var UserTable
     */
    private $userModel;

    /**
     * UserSession constructor.
     *
     * @param Session $session Storage
     * @param UserTable $userModel The User model
     */
    public function __construct(Session $session, UserTable $userModel)
    {
        $this->session = $session;
        $this->userModel = $userModel;
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
     * @param UserEntity $user
     * @return void
     */
    public function setIdentity(UserEntity $user)
    {
        $this->session->set('user', $user);
    }

    /**
     * Returns the identity from storage or null if no identity is available
     *
     * @return UserEntity
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
     * @return UserEntity|null
     */
    public function authenticate($username, $password)
    {
        // Check username and password
        $user = $this->userModel->findByUsername($username);

        if (empty($user)) {
            // User not found
            return null;
        }

        if (!$this->verifyPassword($password, $user->password)) {
            // Credentials invalid
            return null;
        }

        // Clear session data
        $this->session->destroy();
        $this->session->start();

        // Create new session id
        $this->session->regenerateId();

        // Store user settings in session
        $this->setIdentity($user);

        return $user;
    }

    /**
     * Returns secure password hash
     *
     * @param string $password
     * @return string
     */
    public function createPassword($password): string
    {
        return password_hash($password, 1);
    }

    /**
     * Returns true if password and hash is valid
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verifyPassword($password, $hash): bool
    {
        return password_verify($password, $hash);
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
