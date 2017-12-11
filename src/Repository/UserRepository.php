<?php

namespace App\Repository;

use App\Entity\User;
use App\Table\UserTable;
use Exception;
use Illuminate\Database\Connection;

/**
 * User Repository
 */
class UserRepository extends AbstractRepository
{

    /**
     * The Table Gateway object
     *
     * @var UserTable
     */
    private $table;

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
        foreach ($this->table->newQuery()->get() as $row) {
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
        $row = $this->table->newQuery()
            ->where('username', '=', $username)
            ->where('disabled', '=', 0)
            ->first();

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
        return (string)$this->table->newQuery()->insertGetId($user->toArray());
    }

    /**
     * Delete user.
     *
     * @param User $user The user
     * @return int Number of affected rows
     */
    public function delete(User $user): int
    {
        return $this->table->newQuery()->delete($user->id);
    }
}
