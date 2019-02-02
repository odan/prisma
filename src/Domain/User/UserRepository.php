<?php

namespace App\Domain\User;

use App\Repository\BaseRepository;
use DomainException;
use InvalidArgumentException;

/**
 * Repository.
 */
class UserRepository extends BaseRepository
{
    /**
     * Find all users.
     *
     * @return array Rows
     */
    public function findAll(): array
    {
        return $this->fetchAll('users');
    }

    /**
     * Get user by id.
     *
     * @param int $id User id
     *
     * @throws DomainException On error
     *
     * @return array The row
     */
    public function getById(int $id): array
    {
        $row = $this->findUserById($id);

        if (!$row) {
            throw new DomainException(__('User not found: %s', $id));
        }

        return $row;
    }

    /**
     * Find by id.
     *
     * @param int $id The ID
     *
     * @return array The row
     */
    public function findUserById(int $id): array
    {
        return $this->fetchById('users', $id);
    }

    /**
     * Insert or update user.
     *
     * @param array $user
     *
     * @return int User ID
     */
    public function saveUser(array $user): int
    {
        if (!empty($user['id'])) {
            $this->updateUser($user);

            return (int)$user['id'];
        }

        return $this->insertUser($user);
    }

    /**
     * Update user.
     *
     * @param array $user The user data
     *
     * @return bool Success
     */
    public function updateUser(array $user): bool
    {
        if (empty($user['id'])) {
            throw new InvalidArgumentException('User ID required');
        }

        $this->newUpdate('users', $user)->andWhere(['id' => $user['id']])->execute();

        return true;
    }

    /**
     * Insert new user.
     *
     * @param array $user The user
     *
     * @return int The new ID
     */
    public function insertUser(array $user): int
    {
        return (int)$this->newInsert('users', $user)->execute()->lastInsertId();
    }

    /**
     * Delete user.
     *
     * @param int $userId The user ID
     *
     * @return bool Success
     */
    public function deleteUser(int $userId): bool
    {
        $this->newDelete('users')->andWhere(['id' => $userId])->execute();

        return true;
    }
}
