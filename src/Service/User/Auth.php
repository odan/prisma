<?php

namespace App\Service\User;

use App\Entity\UserEntity;
use App\Service\ServiceInterface;
use Odan\Slim\Session\Session;
use PDO;
use RuntimeException;

/**
 * Authentication and authorisation.
 */
class Auth implements ServiceInterface
{
    /**
     * Session.
     *
     * @var Session
     */
    private $session;

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * UserSession constructor.
     *
     * @param Session $session Storage
     * @param PDO $pdo PDO database connection
     */
    public function __construct(Session $session, PDO $pdo)
    {
        $this->session = $session;
        $this->pdo = $pdo;
    }

    /**
     * Returns true if and only if an identity is available from storage.
     *
     * @return bool
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
    public function getId(): int
    {
        $result = $this->getIdentity()->id;

        if (empty($result)) {
            throw new RuntimeException(__('Invalid or empty User-ID'));
        }

        return $result;
    }

    /**
     * Returns the identity from storage or null if no identity is available.
     *
     * @return UserEntity
     */
    public function getIdentity(): UserEntity
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
     * @param string $username
     * @param string $password
     *
     * @return UserEntity|null
     */
    public function authenticate(string $username, string $password): ?UserEntity
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE username = :username AND disabled = 0');
        $statement->execute(['username' => $username]);

        $userRow = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$userRow) {
            return null;
        }

        $user = new UserEntity($userRow);

        if (!$this->verifyPassword($password, $user->password)) {
            return null;
        }

        $this->startUserSession($user);

        return $user;
    }

    /**
     * Returns true if password and hash is valid.
     *
     * @param string $password
     * @param string $hash
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
     * @param UserEntity $user
     *
     * @return void
     */
    protected function startUserSession(UserEntity $user): void
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
     * @param UserEntity $user
     *
     * @return void
     */
    public function setIdentity(UserEntity $user): void
    {
        $this->session->set('user', $user);
    }

    /**
     * Returns secure password hash.
     *
     * @param string $password
     *
     * @return string
     */
    public function createPassword(string $password): string
    {
        return password_hash($password, 1) ?: '';
    }

    /**
     * Check user permission.
     *
     * @param string|array $role (e.g. 'ROLE_ADMIN' or 'ROLE_USER')
     * or array('ROLE_ADMIN', 'ROLE_USER')
     *
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
