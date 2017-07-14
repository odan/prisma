<?php

namespace App\Service\User;

use App\Entity\UserEntity;
use App\Table\UserTable;
use Cake\Database\Connection;

/**
 * User Authentication Adapter
 */
class UserAuthentication extends UserTable
{

    /**
     * Username
     *
     * @var string
     */
    protected $username;

    /**
     * Password
     *
     * @var string
     */
    protected $password;

    /**
     * Token
     *
     * @var Token
     */
    protected $token;

    /**
     * Constructor.
     *
     * @param Connection $db
     * @param Token $token
     * @param string $username
     * @param string $password
     */
    public function __construct(Connection $db, Token $token, $username, $password)
    {
        parent::__construct($db);
        $this->token = $token;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Performs an authentication attempt.
     *
     * @return AuthenticationResult
     */
    public function authenticate()
    {
        $query = $this->newQuery()->select('*')->where([
            'username' => $this->username,
            'disabled' => 0
        ]);

        $userRow = $query->execute()->fetch('assoc');

        if (empty($userRow)) {
            return new AuthenticationResult(AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND);
        }

        $user = new UserEntity($userRow);

        if (!$this->token->verifyHash($this->password, $user->password)) {
            return new AuthenticationResult(AuthenticationResult::FAILURE_CREDENTIAL_INVALID);
        }

        return new AuthenticationResult(AuthenticationResult::SUCCESS, $user);
    }
}
