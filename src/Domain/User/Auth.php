<?php

namespace App\Domain\User;

use Odan\Session\Session;
use RuntimeException;

/**
 * Authentication and authorisation.
 */
class Auth
{
    /**
     * Session.
     *
     * @var Session
     */
    private $session;

    /**
     * @var AuthRepository
     */
    private $authRepository;

    /**
     * Constructor.
     *
     * @param Session $session Storage
     * @param AuthRepository $authRepository The repository
     */
    public function __construct(Session $session, AuthRepository $authRepository)
    {
        $this->session = $session;
        $this->authRepository = $authRepository;
    }

    /**
     * Returns true if and only if an identity is available from storage.
     *
     * @return bool status
     */
    public function hasIdentity(): bool
    {
        return !empty($this->session->get('user'));
    }

    /**
     * Clears the identity from persistent storage.
     *
     * @return void
     */
    public function clearIdentity(): void
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
     * @return int User Id
     */
    public function getUserId(): int
    {
        $result = $this->getUser()->getId();

        if (empty($result)) {
            throw new RuntimeException(__('Invalid or empty User-ID'));
        }

        return $result;
    }

    /**
     * Returns the identity from storage or null if no identity is available.
     *
     * @return User The logged-in user
     */
    public function getUser(): User
    {
        $user = $this->session->get('user');
        if (!$user) {
            throw new RuntimeException('No identity available');
        }

        return $user;
    }

    /**
     * Performs an authentication attempt.
     *
     * @param string $username username
     * @param string $password password
     *
     * @return User|null the user or null
     */
    public function authenticate(string $username, string $password): ?User
    {
        $userRow = $this->authRepository->findUserByUsername($username);

        if (!$userRow) {
            return null;
        }

        $user = User::fromArray($userRow);

        if (!$this->verifyPassword($password, $user->getPassword() ?: '')) {
            return null;
        }

        $this->startUserSession($user);

        return $user;
    }

    /**
     * Returns true if password and hash is valid.
     *
     * @param string $password password
     * @param string $hash stored hash
     *
     * @return bool Success
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Init user session.
     *
     * @param User $user the user
     *
     * @return void
     */
    protected function startUserSession(User $user): void
    {
        // Clear session data
        $this->session->destroy();
        $this->session->start();

        // Create new session id
        $this->session->regenerateId();

        // Store user settings in session
        $this->setIdentity($user);
    }

    /**
     * Set the identity into storage or null if no identity is available.
     *
     * @param User $user the user
     *
     * @return void
     */
    public function setIdentity(User $user): void
    {
        $this->session->set('user', $user);
    }

    /**
     * Returns secure password hash.
     *
     * @param string $password password
     *
     * @return string
     */
    public function createPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT) ?: '';
    }

    /**
     * Accepts a string and returns true if the role is assigned to the user.
     *
     * @param string $role e.g. UserRole::ROLE_ADMIN
     *
     * @return bool Status
     */
    public function hasRole(string $role): bool
    {
        return $role === $this->getUser()->getRole();
    }

    /**
     * Accepts an array with roles and returns true if at least one of the roles
     * in the array is assigned to the user.
     *
     * @param array $roles e.g. [UserRole::ROLE_ADMIN, UserRole::ROLE_USER]
     *
     * @return bool Status
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->getUser()->getRole(), $roles, true);
    }

    /**
     * Checks whether the user is an admin.
     *
     * @return bool Status
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::ROLE_ADMIN);
    }
}
