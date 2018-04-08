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

    $translated = $translator->trans($message);
    $context = array_slice(func_get_args(), 1);
    if (!empty($context)) {
        $translated = vsprintf($translated, $context);
    }

    return $translated;
}

/**
 * Validate mobile number.
 *
 * Example: +41791234567
 *
 * @param string $mobileNumber Mobile number
 *
 * @return bool Status
 */
function is_mobile_number($mobileNumber = null): bool
{
    return (bool)preg_match('/^\+[0-9]{11,}$/', $mobileNumber);
}

/**
 * Determine if date string is a valid date in that format.
 *
 * @param string $time datetime
 * @param string $format format (d.m.Y)
 *
 * @return bool
 */
function is_time($time, $format = 'd.m.Y')
{
    $date = DateTime::createFromFormat($format, $time);
    $result = $date && $date->format($format) == $time;

    return $result;
}

/**
 * Returns true if date is valid (d.m.Y).
 *
 * @param string $date date
 *
 * @return bool
 */
function is_date($date)
{
    if (!is_time($date, 'd.m.Y')) {
        return false;
    }
    if (!preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/', $date)) {
        return false;
    }

    return true;
}

/**
 * Returns true if date is valid ISO DateTime (Y-m-d H:i:s).
 *
 * @param string $dateTime dateTime
 *
 * @return bool
 */
function is_iso_datetime($dateTime)
{
    if (!preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2}$/', $dateTime)) {
        return false;
    }
    if (!is_time($dateTime, 'Y-m-d H:i:s')) {
        return false;
    }

    return true;
}

/**
 * Any positive integer, excluding 0.
 *
 * @param mixed $value The value
 *
 * @return bool Status
 */
function is_positive_integer($value)
{
    return (bool)preg_match('/^[1-9]\d*$/', $value);
}

/**
 * Any positive integer (1234) and positive float (3.14), excluding 0.
 *
 * @param mixed $value The value
 *
 * @return bool Status
 */
function is_positive_float($value)
{
    return (bool)preg_match('/^[1-9]\d*$/', $value);
}
