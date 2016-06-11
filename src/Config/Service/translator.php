<?php

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Loader\MoFileLoader;

//
// Create translator
//
$locale = 'en_US';
//$locale = 'de_DE';
$domain = 'messages';
$translator = new Translator($locale, new MessageSelector());
$translator->addLoader('mo', new MoFileLoader());

// Set locale
$moFile = sprintf('%s/../../Locale/%s_%s.mo', __DIR__, $locale, $domain);
if (file_exists($moFile)) {
    $translator->addResource('mo', $moFile, $locale, $domain);
    $translator->setLocale($locale);
}

// Inject translator into function
__(null, null, $translator);

//$test = __('Yes');

/**
 * Text translation (I18n)
 *
 * @param string $message
 * @param array $context
 * @param Translator $translator Translator
 * @return string
 *
 * <code>
 * echo __('Hello');
 * echo __('There are %s persons logged', [7]);
 * </code>
 */
function __($message, $context = array(), Translator $translator = null)
{
    /* @var $tr Translator */
    static $tr = null;

    // Dependency injection for this function
    if ($translator !== null) {
        $tr = $translator;
        return null;
    }
    $message = $tr->trans($message);
    if (!empty($context)) {
        $message = vsprintf($message, $context);
    }
    return $message;
}
return $translator;


