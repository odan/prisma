<?php

/**
 * Convert all applicable characters to HTML entities
 *
 * @param string $text
 * @return string
 */
function gh($text)
{
    // Skip empty strings
    if ($text === null || $text === '') {
        return '';
    }

    // Convert to utf-8
    if (!mb_check_encoding($text, 'UTF-8')) {
        $text = mb_convert_encoding($text, 'UTF-8');
    }

    $text = htmlentities($text, ENT_QUOTES, "UTF-8");

    // Convert non printable and non ascii chars to numeric entity
    // This will match a single non-ASCII character
    // This is a valid PCRE (Perl-Compatible Regular Expression).
    $text = preg_replace_callback('/[^\x20-\x7E]/u', function ($match) {
        return mb_encode_numericentity($match[0], array(0x0, 0xffff, 0, 0xffff), 'UTF-8');
    }, $text);

    return $text;
}

/**
 * Write html encoded string
 *
 * @param string $str
 */
function wh($str)
{
    echo gh($str);
}

/**
 * URL Encoding: Write URL encoded string
 *
 * @param string $str
 */
function wu($str)
{
    echo urlencode($str);
}

/**
 * URL Encoding
 *
 * @param string $str
 */
function gu($str)
{
    return urlencode($str);
}

/**
 * HTML Attribute Encoding
 *
 * @param string $str string to encode
 */
function ga($str)
{
    return htmlspecialchars($str);
}

/**
 * HTML Attribute Encoding: Write attribute encoded string
 *
 * @param string $str string to encode and print
 */
function wa($str)
{
    echo htmlspecialchars($str);
}

/**
 * Returns true if the variable is blank.
 * When you need to accept these as valid, non-empty values:
 *
 * - 0 (0 as an integer)
 * - 0.0 (0 as a float)
 * - "0" (0 as a string)
 *
 * @param mixed $value
 * @return boolean
 */
function blank($value)
{
    return empty($value) && !is_numeric($value);
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
 * Returns a random UUID
 *
 * @return string
 */
function uuid()
{
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for time low
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            // 16 bits for time mid
            mt_rand(0, 0xffff),
            // 16 bits for time hi and version,
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for clk seq hi res,
            // 8 bits for clk_seq_low,
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for node
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

if (!function_exists('random_bytes')) {

    /**
     * Fallback for random_bytes (>= PHP 7)
     *
     * @param int $length Number of bytes
     * @return string Bytes
     */
    function random_bytes($length)
    {
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= chr(mt_rand(0, 255));
        }
        return $result;
    }
}

/**
 * Validate E-Mail address
 *
 * @param string $email
 * @return bool
 */
function is_email($email = null)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Returns a trimmed array
 *
 * @param array $array
 * @return array
 */
function trim_array($array)
{
    if (is_array($array)) {
        foreach ($array as $key => $val) {
            $array[$key] = trim_array($val);
        }
        return $array;
    } else {
        if (is_string($array)) {
            $array = trim($array);
        }
        return $array;
    }
}

/**
 * Read php file
 *
 * @param string $file Filename
 * @return mixed
 */
function read($file)
{
    return require $file;
}
