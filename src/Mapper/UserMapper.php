<?php

namespace App\Mapper;

use App\Entity\UserEntity;
use Exception;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Class UserMapper
 */
class UserMapper extends AbstractMapper
{
    /**
     * Table
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Returns a collection of User entities.
     *
     * @return UserEntity[]
     */
    public function findAll(): array
    {
        $result = [];
        foreach ($this->fetchAll() as $row) {
            $result[] = new UserEntity($row);
        }

        return $result;
    }

    /**
     * Find entity by id.
     *
     * @param int|string $id The ID
     * @return UserEntity|null The entity
     */
    public function findById($id)
    {
        $row = $this->fetchById($id);
        if (empty($row)) {
            return null;
        }

        return new UserEntity($row);
    }

    /**
     * Get user by id
     *
     * @param string $id User id
     * @return UserEntity A row
     * @throws Exception On error
     */
    public function getById(string $id): UserEntity
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
     * @return UserEntity|null User
     */
    public function findByUsername($username)
    {
        $row = $this->newQuery()->where('username', '=', $username)->where('disabled', '=', 0)->first();

        if (empty($row)) {
            return null;
        }

        return new UserEntity($row);
    }

    /**
     * Insert new user.
     *
     * @param UserEntity $user The user
     * @return string The new ID
     */
    public function insertUser(UserEntity $user): string
    {
        return (string)$this->newQuery()->insertGetId($user->toArray());
    }

    /**
     * Update user.
     *
     * @param UserEntity $user The user
     * @return int Number of affected rows
     */
    public function updateUser(UserEntity $user): int
    {
        if (empty($user->id)) {
            throw new RuntimeException('User ID required');
        }

        return $this->newQuery()->where('id', '=', $user->id)->update($user->toArray());
    }

    /**
     * Insert or update user.
     *
     * @param UserEntity $user
     * @return int
     */
    public function saveUser(UserEntity $user)
    {
        if ($user->id) {
            return $this->updateUser($user);
        } else {
            $this->insertUser($user);
            return 1;
        }
    }

    /**
     * Delete user.
     *
     * @param string $userId The user ID
     * @return int Number of affected rows
     */
    public function deleteUser(string $userId): int
    {
        return $this->newQuery()->delete($userId);
    }
}
