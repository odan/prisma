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

/**
 * Return Array element value (get value)
 *
 * @param array $arr
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function gv($arr, $key, $default = null)
{
    return isset($arr[$key]) ? $arr[$key] : $default;
}

/**
 * Encode an array to JSON
 *
 * Also makes sure the data is encoded in UTF-8.
 *
 * @param array $data The array to encode in JSON.
 * @param int $options The encoding options.
 * @return string The JSON encoded string.
 */
function encode_json($data, $options = 0)
{
    return json_encode(encode_utf8($data), $options);
}

/**
 * Json decoder
 *
 * @param string $json Json string
 * @return mixed
 */
function decode_json($json)
{
    return json_decode($json, true);
}

/**
 * Encodes an ISO-8859-1 string or array to UTF-8.
 *
 * @param mixed $data String or array to convert.
 * @return mixed Encoded data.
 */
function encode_utf8($data)
{
    if ($data === null || $data === '') {
        return $data;
    }

    if (is_array($data)) {
        foreach ($data as $strKey => $mixVal) {
            $data[$strKey] = encode_utf8($mixVal);
        }
        return $data;
    } else {
        if (!mb_check_encoding($data, 'UTF-8')) {
            return mb_convert_encoding($data, 'UTF-8');
        } else {
            return $data;
        }
    }
}

/**
 * Returns a ISO-8859-1 encoded string or array.
 *
 * @param mixed $mix
 * @return mixed
 */
function encode_iso($mix)
{
    if ($mix === null || $mix === '') {
        return $mix;
    }
    if (is_array($mix)) {
        foreach ($mix as $str_key => $str_val) {
            $mix[$str_key] = encode_iso($str_val);
        }
        return $mix;
    } else {
        if (mb_check_encoding($mix, 'UTF-8')) {
            return mb_convert_encoding($mix, 'ISO-8859-1', 'auto');
        } else {
            return $mix;
        }
    }
}

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
function __($message, $context = array(), \Symfony\Component\Translation\Translator $translator = null)
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
