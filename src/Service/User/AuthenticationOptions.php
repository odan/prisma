<?php

namespace App\Service\User;

/**
 * Authentication options (A options pattern)
 */
class AuthenticationOptions
{
    /**
     * @var string Locale path
     */
    public $localePath;

    /**
     * @var string The secret key
     */
    public $secret;
}
