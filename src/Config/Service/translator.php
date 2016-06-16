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

return $translator;


