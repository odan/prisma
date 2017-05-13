<?php

namespace App\Service\User;

use App\Service\Base\BaseService;
use Cake\Database\Connection;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * User Session Handler
 */
class UserSession extends BaseService
{

    /**
     * Secret session key
     *
     * @var Session
     */
    protected $session;

    /**
     * Database
     *
     * @var Connection
     */
    protected $db;

    /**
     * Secret session key
     *
     * @var string
     */
    protected $secret = '';

    /**
     * UserSession constructor.
     * @param Session $session
     * @param Connection $db
     */
    public function __construct(Session $session, Connection $db)
    {
        $this->session = $session;
        $this->db = $db;
    }

    /**
     * Change user session locale
     *
     * @param string $locale
     * @param string $domain
     * @return bool Status
     */
    public function setLocale($locale = 'en_US', $domain = 'messages')
    {
        $this->set('user.locale', $locale);
        $this->set('user.domain', $domain);

        set_locale($locale, $domain);
        return true;
    }

    /**
     * Get locale
     *
     * @param string $default
     * @return  string Locale
     */
    public function getLocale($default = 'en_US')
    {
        $result = $this->get('user.locale');
        if (empty($result)) {
            $result = $default;
        }
        return $result;
    }

    /**
     * Set secret session key
     *
     * @param string $secret secret session key
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * Login user with username and password
     *
     * @param string $username Username
     * @param string $password Password
     * @return bool Status
     */
    public function login($username, $password)
    {
        // Check username and password
        $user = $this->getUserByLogin($username, $password);

        if (empty($user)) {
            return false;
        }

        // Login ok
        // Create new session id
        $this->session->invalidate();

        // Store user settings in session
        $this->set('user.id', $user['id']);
        $this->set('user.role', $user['role']);
        $this->setLocale($user['locale']);
        return true;
    }

    /**
     * Logout user session
     *
     * @return void
     */
    public function logout()
    {
        $this->set('user.id', null);
        $this->set('user.role', null);
        $this->setLocale();

        // Clears all session data and regenerates session ID
        $this->session->invalidate();
    }

    /**
     * Returns secure password hash
     *
     * @param string $password
     * @param int $algo
     * @param array $options
     * @return string
     */
    public function createHash($password, $algo = 1, $options = array())
    {
        return password_hash($password, $algo, $options);
    }

    /**
     * Returns true if password and hash is valid
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verifyHash($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Check if token is correct for this string
     *
     * @param string $value
     * @param string $token
     * @return boolean
     */
    public function checkToken($value, $token)
    {
        $realHash = $this->getToken($value);
        $result = ($token === $realHash);
        return $result;
    }

    /**
     * Generate Hash-Token from string
     *
     * @param string $value
     * @param string $secret
     * @return string
     */
    public function getToken($value, $secret = null)
    {
        if ($secret === null) {
            $secret = $this->secret;
        }
        // Create real key for value
        $sessionId = $this->session->getId();
        $realHash = sha1($value . $sessionId . $secret);
        return $realHash;
    }

    /**
     * Set user info
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->session->set($key, $value);
    }

    /**
     * Get current user information
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->session->get($key, $default);
    }

    /**
     * Check user permission.
     *
     * @param string|array $role (e.g. 'ROLE_ADMIN' or 'ROLE_USER')
     * or array('ROLE_ADMIN', 'ROLE_USER')
     * @return bool Status
     */
    public function is($role)
    {
        // Current user role
        $userRole = $this->get('user.role');

        // Full access for admin
        if ($userRole === 'ROLE_ADMIN') {
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

    /**
     * Check if user is authenticated (logged in)
     *
     * @return bool Status
     */
    public function isValid()
    {
        $id = $this->get('user.id');
        return !empty($id);
    }

    /**
     * Returns user by username and password
     *
     * @param string $username
     * @param string $password
     * @return array|null
     */
    protected function getUserByLogin($username, $password)
    {
        $query = $this->db->newQuery()
                ->select(['*'])
                ->from('users')
                ->where(['username' => $username])
                ->where(['disabled' => 0]);

        $row = $query->execute()->fetch('assoc');

        if (empty($row) || !$this->verifyHash($password, $row['password'])) {
            $row = null;
        }
        return $row;
    }
}
