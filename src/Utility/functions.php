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

/**
 * Shorthand for now function
 *
 * @return string ISO date time (Y-m-d H:i:s)
 */
function now()
{
    return date('Y-m-d H:i:s');
}

/**
 * Returns a `UUID` v4 created from a cryptographically secure random value.
 *
 * @see https://www.ietf.org/rfc/rfc4122.txt
 * @return string RFC 4122 UUID
 * @throws Exception
 */
function uuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        random_int(0, 65535),
        random_int(0, 65535),
        // 16 bits for "time_mid"
        random_int(0, 65535),
        // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
        random_int(0, 4095) | 0x4000,
        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        random_int(0, 0x3fff) | 0x8000,
        // 48 bits for "node"
        random_int(0, 65535),
        random_int(0, 65535),
        random_int(0, 65535)
    );
}
