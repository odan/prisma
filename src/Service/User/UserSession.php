<?php

namespace App\Service\User;

use App\Service\Base\BaseService;
use App\Utility\Database;
use Aura\Session\Segment;
use Aura\Session\Session;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

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
     * @var Database
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
     * @param Database $db Database
     * @param string $secret Secret session key
     */
    public function __construct(Session $session, Database $db, $secret = '')
    {
        $this->session = $session;
        $this->segment = $this->session->getSegment('app');
        $this->db = $db;
        $this->secret = $secret;
        $this->token = $this->createToken($secret);
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
     * Get user Id.
     *
     * @return int User Id
     */
    public function getId()
    {
        return (int)$this->get('user.id');
    }

    /**
     * Get current user information
     *
     * @param string $key Key
     * @param mixed $default Default value
     * @return mixed Value
     */
    public function get($key, $default = null)
    {
        return $this->segment->get($key, $default);
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
     * Set locale
     *
     * @param string $locale
     * @param string $domain
     * @return void
     */
    protected function setTranslatorLocale($locale = 'en_US', $domain = 'messages')
    {
        $settings = container()->get('settings');
        $moFile = sprintf('%s/%s_%s.mo', $settings['locale']['path'], $locale, $domain);

        $translator = new Translator($locale, new MessageSelector());
        $translator->addLoader('mo', new MoFileLoader());

        $translator->addResource('mo', $moFile, $locale, $domain);
        $translator->setLocale($locale);

        // Inject translator into function
        container()->offsetSet('translator', $translator);
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
        $this->set('user.id', $user->id);
        $this->set('user.role', $user->role);
        $this->setLocale($user->locale);
        return true;
    }

    /**
     * Set user info
     *
     * @param string $key Key
     * @param mixed $value Value
     * @return void
     */
    public function set($key, $value)
    {
        $this->segment->set($key, $value);
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
        $this->setTranslatorLocale($locale, $domain);
        return true;
    }

    /**
     * Logout user session
     *
     * @return bool Status
     */
    public function logout()
    {
        $this->set('user.id', null);
        $this->set('user.role', null);
        $this->setLocale();

        // Clears all session data and regenerates session ID
        $this->session->destroy();

        return true;
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
