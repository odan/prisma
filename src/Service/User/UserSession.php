<?php

namespace App\Service\User;

use Aura\Session\Segment;
use Aura\Session\Session;
use Odan\Config\ConfigBag;
use Symfony\Component\Translation\Translator;

/**
 * User Session Handler
 */
class UserSession
{

    /**
     * Session
     *
     * @var Session
     */
    private $session;

    /**
     * Session segment
     *
     * @var Segment
     */
    private $segment;

    /**
     * Translator
     *
     * @var Translator
     */
    private $translator;

    /**
     * Locale path
     *
     * @var string
     */
    private $localePath;

    /**
     * @var Token
     */
    private $token;

    /**
     * @var string
     */
    private $secret = '';

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserSession constructor.
     *
     * @param Session $session Storage
     * @param UserRepository $userRepository The User repository
     * @param Translator $translator Translator
     * @param ConfigBag $config The application settings
     */
    public function __construct(Session $session, UserRepository $userRepository, Translator $translator, ConfigBag $config)
    {
        $this->session = $session;
        $this->segment = $this->session->getSegment('app');
        $this->userRepository = $userRepository;
        $this->translator = $translator;
        $settings = $config->export();
        $this->localePath = $settings['locale']['path'];
        $this->secret =  $settings['app']['secret'];
        $this->token = $this->createToken($this->secret);
    }

    /**
     * Create token object.
     *
     * @param string $secret
     * @return Token
     */
    protected function createToken($secret): Token
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
        return (string)$this->get('user.id');
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
    public function getToken(): Token
    {
        return $this->token;
    }

    /**
     * Get locale
     *
     * @param string $default
     * @return string Locale
     */
    public function getLocale($default = 'en_US'): string
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
    protected function setTranslatorLocale($locale = 'en_US', $domain = 'messages'): void
    {
        $moFile = sprintf('%s/%s_%s.mo', $this->localePath, $locale, $domain);

        $this->translator->addResource('mo', $moFile, $locale, $domain);
        $this->translator->setLocale($locale);
    }

    /**
     * Login user with username and password
     *
     * @param string $username Username
     * @param string $password Password
     * @return bool Status
     */
    public function login($username, $password): bool
    {
        // Check username and password
        $auth = new AuthenticationService($this->userRepository, $this->token, $username, $password);
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
    public function set($key, $value): void
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
    public function setLocale($locale = 'en_US', $domain = 'messages'): bool
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
    public function logout(): bool
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
    public function hasRole($role): bool
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
    public function isValid(): bool
    {
        $id = $this->get('user.id');

        return !empty($id);
    }
}
