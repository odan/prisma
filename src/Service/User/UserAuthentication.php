<?php

namespace App\Service\User;

use App\Table\UserTable;
use App\Utility\Database;

/**
 * User Authentication Adapter
 */
class UserAuthentication extends UserTable
{

    /**
     * Database
     *
     * @var Database
     */
    protected $db;

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
     * @var Token
     */
    protected $token;

    /**
     * Constructor.
     *
     * @param Database $db
     * @param Token $token
     * @param string $username
     * @param string $password
     */
    public function __construct(Database $db, Token $token, $username, $password)
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
        $query = $this->newSelect()
            ->cols(['*'])
            ->where('username = ?', $this->username)
            ->where('disabled = ?', 0);

        $userRow = $this->executeQuery($query)->fetch();

        if (empty($userRow)) {
            return new AuthenticationResult(AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND);
        }

        if (!$this->token->verifyHash($this->password, $userRow['password'])) {
            return new AuthenticationResult(AuthenticationResult::FAILURE_CREDENTIAL_INVALID);
        }

        return new AuthenticationResult(AuthenticationResult::SUCCESS, $userRow);
    }
}
