<?php

namespace App\Service\User;

class Token
{
    /**
     * Secret session key
     *
     * @var string
     */
    protected $secret = '';

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
    public function createHash($password, $algo = 1, $options = array())
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
    public function verifyHash($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Check if token is correct for this string
     *
     * @param string $value
     * @param string $token
     * @return boolean
     */
    public function checkToken($value, $token)
    {
        $realHash = $this->getToken($value);
        $result = ($token === $realHash);
        return $result;
    }

    /**
     * Generate Hash-Token from string
     *
     * @param string $value
     * @return string
     */
    public function getToken($value)
    {
        // Create real key for value
        $sessionId = $this->session->getId();
        $realHash = sha1($value . $sessionId . $this->secret);
        return $realHash;
    }
}
