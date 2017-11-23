<?php

namespace App\Service\User;

/**
 * Class Token
 */
class Token
{
    /**
     * Secret session key
     *
     * @var string
     */
    private $secret = '';

    /**
     * Token constructor.
     *
     * @param string $secret
     */
    public function __construct($secret = '')
    {
        $this->secret = $secret;
    }

    /**
     * Returns secure password hash
     *
     * @param string $password
     * @param int $algo
     * @param array $options
     * @return string
     */
    public function hash($password, $algo = 1, $options = array()): string
    {
        return password_hash($password, $algo, $options);
    }

    /**
     * Returns true if password and hash is valid
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verifyHash($password, $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Check if token is correct for this string
     *
     * @param string $value Value
     * @param string $token Token
     * @return bool Status
     */
    public function check($value, $token): bool
    {
        $realHash = $this->create($value);
        return hash_equals($realHash, $token);
    }

    /**
     * Generate Hash-Token from string
     *
     * @param string $value
     * @return string
     */
    public function create($value): string
    {
        return sha1($value . $this->secret);
    }
}
