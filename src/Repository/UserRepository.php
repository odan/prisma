<?php

namespace App\Repository;

use App\Entity\User;
use App\Table\UserTable;
use Exception;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use RuntimeException;

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
    private $userTable;

    /**
     * Constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->userTable = new UserTable($db);
    }

    /**
     * Returns a collection of User entities.
     *
     * @return Collection|User[]
     */
    public function findAll(): Collection
    {
        return $this->userTable->fetchAll()->map(function ($row) {
            return new User($row);
        });
    }

    /**
     * Find entity by id.
     *
     * @param int|string $id The ID
     * @return User|null The entity
     */
    public function findById($id)
    {
        $row = $this->userTable->fetchById($id);
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
        $row = $this->userTable->newQuery()->where('username', '=', $username)->where('disabled', '=', 0)->first();

        if (empty($row)) {
            return null;
        }

        return new User($row);
    }

    /**
     * Insert new user.
     *
     * @param User $user The user
     * @return string The new ID
     */
    public function insert(User $user): string
    {
        return (string)$this->userTable->newQuery()->insertGetId($user->toArray());
    }

    /**
     * Update user.
     *
     * @param User $user The user
     * @return int Number of affected rows
     */
    public function update(User $user): int
    {
        if (empty($user->id)) {
            throw new RuntimeException('User ID required');
        }

        return $this->userTable->newQuery()->where('id', '=', $user->id)->update($user->toArray());
    }

    /**
     * Delete user.
     *
     * @param User $user The user
     * @return int Number of affected rows
     */
    public function delete(User $user): int
    {
        return $this->userTable->newQuery()->delete($user->id);
    }
}
