<?php

namespace App\Service\User;

use App\Container\AppContainer;
use App\Service\Base\BaseService;

/**
 * User Session Handler
 */
class UserSession extends BaseService
{

    /**
     * Secret session key
     *
     * @var string
     */
    protected $secret = '';

    /**
     * Constructor
     *
     * @param AppContainer $app
     * @return void
     */
    public function __construct(AppContainer $app)
    {
        parent::__construct($app);
        $this->setSecret($app->config['app']['secret']);
    }

    /**
     * Change user session locale
     *
     * @param string $locale
     * @param string $domain
     * @return bool
     */
    public function setLocale($locale = 'en_US', $domain = 'messages')
    {
        $this->set('user.locale', $locale);
        $this->set('user.domain', $domain);

        $translator = $this->app->translator;
        $moFile = sprintf('%s/../../Locale/%s_%s.mo', __DIR__, $locale, $domain);
        if (file_exists($moFile)) {
            $translator->addResource('mo', $moFile, $locale, $domain);
        }
        $translator->setLocale($locale);

        //$test = __('Hello');
        return true;
    }

    /**
     * Get locale
     *
     * @param string $default
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
     * @param array $params
     * @return bool
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
        $this->app->session->invalidate();

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
        $this->app->session->invalidate();
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
        if (function_exists('password_hash')) {
            // php >= 5.5
            $hash = password_hash($password, $algo, $options);
        } else {
            $salt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
            $salt = base64_encode($salt);
            $salt = str_replace('+', '.', $salt);
            $hash = crypt($password, '$2y$10$' . $salt . '$');
        }
        return $hash;
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
        if (function_exists('password_verify')) {
            // php >= 5.5
            $result = password_verify($password, $hash);
        } else {
            $hash2 = crypt($password, $hash);
            $result = $hash == $hash2;
        }
        return $result;
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
        $sessionId = $this->app->session->getId();
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
        $this->app->session->set($key, $value);
    }

    /**
     * Get current user information
     *
     * @param string $key
     * @return mixed
     */
    public function get($key, $default = '')
    {
        $mixReturn = $this->app->session->get($key, $default);
        return $mixReturn;
    }

    /**
     * Check user permission
     *
     * @param string|array $role (e.g. 'ROLE_ADMIN' or 'ROLE_USER')
     * or array('ROLE_ADMIN', 'ROLE_USER')
     * @return boolean
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
     * @return boolean
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
        $query = $this->app->db->newQuery()
                ->select(['*'])
                ->from('user')
                ->where(['username' => $username])
                ->where(['disabled' => 0]);

        $row = $query->execute()->fetch('assoc');

        if (empty($row) || !$this->verifyHash($password, $row['password'])) {
            $row = null;
        }
        return $row;
    }
}
