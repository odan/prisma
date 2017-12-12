<?php

namespace App\Service\Auth;

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
    public function create($password, $algo = 1, $options = array()): string
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
    public function verify($password, $hash): bool
    {
        return password_verify($password, $hash);
    }
}
