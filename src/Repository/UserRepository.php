<?php

namespace App\Repository;

use App\Entity\UserEntity;
use RuntimeException;

/**
 * Users repository.
 */
final class UserRepository extends ApplicationRepository
{
    /**
     * Returns a collection of User entities.
     *
     * @return UserEntity[]
     */
    public function findAll(): array
    {
        $result = [];
        foreach ($this->newSelect('users')->get() as $row) {
            $result[] = new UserEntity($row);
        }

        return $result;
    }

    /**
     * Get user by id.
     *
     * @param string $id User id
     *
     * @throws RuntimeException On error
     *
     * @return UserEntity A row
     */
    public function getById(string $id): UserEntity
    {
        if (!$user = $this->findById($id)) {
            throw new RuntimeException(__('User not found: %s', $id));
        }

        return $user;
    }

    /**
     * Find entity by id.
     *
     * @param int|string $id The ID
     *
     * @return UserEntity|null The entity
     */
    public function findById($id)
    {
        $row = $this->fetchById('users', $id);
        if (empty($row)) {
            return null;
        }

        return new UserEntity($row);
    }

    /**
     * Find user by username.
     *
     * @param string $username Username
     *
     * @return UserEntity|null User
     */
    public function findByUsername($username)
    {
        $row = $this->newSelect('users')
            ->where('username', '=', $username)
            ->where('disabled', '=', 0)
            ->first();

        if (empty($row)) {
            return null;
        }

        return new UserEntity($row);
    }

    /**
     * Insert or update user.
     *
     * @param UserEntity $user
     *
     * @return int
     */
    public function saveUser(UserEntity $user)
    {
        if ($user->id) {
            return $this->updateUser($user);
        }
        $this->insertUser($user);

        return 1;
    }

    /**
     * Update user.
     *
     * @param UserEntity $user The user
     *
     * @return int Number of affected rows
     */
    public function updateUser(UserEntity $user): int
    {
        if (empty($user->id)) {
            throw new RuntimeException('User ID required');
        }

        return $this->newSelect('users')->where('id', '=', $user->id)->update($user->toArray());
    }

    /**
     * Insert new user.
     *
     * @param UserEntity $user The user
     *
     * @return string The new ID
     */
    public function insertUser(UserEntity $user): string
    {
        return (string)$this->db->table('users')->insertGetId($user->toArray());
    }

    /**
     * Delete user.
     *
     * @param int $userId The user ID
     *
     * @return int Number of affected rows
     */
    public function deleteUser(int $userId): int
    {
        return $this->db->table('users')->delete($userId);
    }
}
