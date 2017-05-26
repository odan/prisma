<?php

namespace App\Service\User;

use Aura\Session\Segment;
use Aura\Session\Session;
use App\Service\Base\BaseService;
use Cake\Database\Connection;

/**
 * User Session Handler
 */
class UserSession extends BaseService
{

    /**
     * Session
     *
     * @var Session
     */
    protected $session;

    /**
     * Session segment
     *
     * @var Segment
     */
    protected $segment;

    /**
     * Database
     *
     * @var Connection
     */
    protected $db;

    /**
     * @var Token
     */
    protected $token;

    /**
     * @var string
     */
    protected $secret = '';

    /**
     * UserSession constructor.
     *
     * @param Session $session Storage
     * @param Connection $db Database
     * @param string $secret Secret session key
     */
    public function __construct(Session $session, Connection $db, $secret = '')
    {
        $this->session = $session;
        $this->segment = $this->session->getSegment('app');
        $this->db = $db;
        $this->secret = $secret;
        $this->token = $this->createToken($secret);
    }

    /**
     * Get user Id.
     *
     * @return int
     */
    public function getId()
    {
        return (int)$this->get('user.id');
    }

    /**
     * Return token object.
     *
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Create token object.
     *
     * @param $secret
     * @return Token
     */
    protected function createToken($secret)
    {
        return new Token($this->session->getId() . $secret);
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
     * Login user with username and password
     *
     * @param string $username Username
     * @param string $password Password
     * @return bool Status
     */
    public function login($username, $password)
    {
        // Check username and password
        $auth = new UserAuthentication($this->db, $this->token, $username, $password);
        $authResult = $auth->authenticate();

        if (!$authResult->isValid()) {
            return false;
        }

        $user = $authResult->getIdentity();

        // Clear session data
        $this->segment->clear();
        $this->segment->clearFlash();
        $this->segment->clearFlashNow();

        // Create new session id
        $this->session->clear();
        $this->session->start();

        // Create new token
        $this->token = $this->createToken($this->secret);

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
        $this->session->destroy();
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
        $this->segment->set($key, $value);
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
        return $this->segment->get($key, $default);
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
        if ($userRole === UserRole::ROLE_ADMIN) {
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
}
