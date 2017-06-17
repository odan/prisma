<?php

namespace App\Service\User;

use App\Service\Base\BaseService;
use App\Table\UserTable;
use Cake\Database\Connection;

/**
 * User Authentication Adapter
 */
class UserAuthentication extends BaseService
{

    /**
     * Database
     *
     * @var Connection
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
     * @param Connection $db
     * @param Token $token
     * @param string $username
     * @param string $password
     */
    public function __construct(Connection $db, Token $token, $username, $password)
    {
        $this->db = $db;
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
        $userTable = new UserTable($this->db);
        $query = $userTable->newQuery()
            ->select(['*'])
            ->where(['username' => $this->username])
            ->where(['disabled' => 0]);

        $userRow = $query->execute()->fetch('assoc');

        if (empty($userRow)) {
            return new AuthenticationResult(AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND);
        }

        if (!$this->token->verifyHash($this->password, $userRow['password'])) {
            return new AuthenticationResult(AuthenticationResult::FAILURE_CREDENTIAL_INVALID);
        }

        return new AuthenticationResult(AuthenticationResult::SUCCESS, $userRow);
    }
}
