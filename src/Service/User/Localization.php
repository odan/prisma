<?php

namespace App\Service\User;

use Aura\Session\Segment;
use Symfony\Component\Translation\Translator;

/**
 * Localization
 */
class Localization
{
    /**
     * @var Segment
     */
    private $session;

    /**
     * @var string Locale path
     */
    public $localePath;

    /**
     * Translator
     *
     * @var Translator
     */
    private $translator;

    /**
     * Constructor.
     *
     * @param Translator $translator
     * @param Segment $session
     * @param string $localePath
     */
    public function __construct(Translator $translator, Segment $session, string $localePath)
    {
        $this->translator = $translator;
        $this->session = $session;
        $this->localePath = $localePath;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return void
     */
    private function setLocale(string $locale):void
    {
        $this->session->set('locale', $locale);
    }

    /**
     * Get local
     *
     * @return string|null
     */
    public function getLocale()
    {
        return $this->session->get('locale');
    }

    /**
     * Set text domain.
     *
     * @param string $domain
     * @return void
     */
    private function setDomain(string $domain):void
    {
        $this->session->set('domain', $domain);
    }

    /**
     * Get text domain.
     *
     * @return string|null
     */
    public function getDomain()
    {
        return $this->session->get('domain');
    }

    /**
     * Change user session locale
     *
     * @param string $locale e.g. en_US
     * @param string $domain e.g. messages
     * @return bool Status
     */
    public function setLanguage($locale, $domain = 'messages'): bool
    {
        $this->setLocale($locale);
        $this->setDomain($domain);
        $this->setTranslatorLocale($locale, $domain);

        return true;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @param string $domain
     * @return void
     */
    private function setTranslatorLocale(string $locale, string $domain = 'messages'): void
    {
        $moFile = sprintf('%s/%s_%s.mo', $this->localePath, $locale, $domain);
        $this->translator->addResource('mo', $moFile, $locale, $domain);
        $this->translator->setLocale($locale);
    }
}
