<?php

namespace App\Service\User;

use App\Entity\User;
use App\Repository\AbstractRepository;
use App\Table\UserTable;
use Exception;
use Odan\Database\Connection;

/**
 * User Repository
 */
class UserRepository extends AbstractRepository
{

    /**
     * Constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->table = new UserTable($db);
    }

    /**
     * Returns an array with all entities.
     *
     * @return User[] Array with entities
     */
    public function findAll()
    {
        $users = [];
        foreach ($this->table->fetchAll() as $row) {
            $users[] = new User($row);
        }

        return $users;
    }

    /**
     * Find entity by id.
     *
     * @param int|string $id The ID
     * @return User|null The entity
     */
    public function findById($id)
    {
        $row = $this->table->fetchById($id);
        if (empty($row)) {
            return null;
        }

        return new User($row);
    }

    /**
     * Get user by id
     *
     * @param string $id User id
     * @return User A row
     * @throws Exception On error
     */
    public function getById($id)
    {
        if (!$user = $this->findById($id)) {
            throw new Exception(__('User not found: %s', $id));
        }
        return $user;
    }

    /**
     * Find user by username.
     *
     * @param string $username Username
     * @return User|null User
     */
    public function findByUsername($username)
    {
        $row = $this->table->select()->columns('*')
            ->where('username', '=', $username)
            ->where('disabled', '=', 0)
            ->execute()
            ->fetch();

        if (empty($row)) {
            return null;
        }

        return new User($row);
    }

    /**
     * Insert new user.
     *
     * @param User $user The user
     * @return string The new ID.
     */
    public function insert(User $user): string
    {
        return $this->table->insert()->insertGetId($user->toArray());
    }

    /**
     * Delete user.
     *
     * @param User $user The user
     * @return bool success
     */
    public function delete(User $user)
    {
        return $this->table->delete($user->id)->execute();
    }
}
