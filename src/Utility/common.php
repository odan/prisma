<?php

/**
 * Services and helper functions.
 */
use Symfony\Component\Translation\Translator;

/**
 * Text translation (I18n).
 *
 * @param mixed|Translator $message
 *
 * @return string
 *
 * <code>
 * echo __('Hello');
 * echo __('There are %s users logged in.', 7);
 * </code>
 */
function __($message)
{
    /* @var Translator $translator */
    static $translator = null;
    if ($message instanceof Translator) {
        $translator = $message;

        return '';
    }

    if ($translator === null) {
        throw new RuntimeException('Translator not initialized');
    }

    $translated = $translator->trans($message);
    $context = array_slice(func_get_args(), 1);
    if (!empty($context)) {
        $translated = vsprintf($translated, $context);
    }

    return $translated;
}
