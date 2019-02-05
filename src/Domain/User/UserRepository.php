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
     * @param int $userId User id
     *
     * @throws DomainException On error
     *
     * @return array The row
     */
    public function getById(int $userId): array
    {
        $row = $this->findUserById($userId);

        if (!$row) {
            throw new DomainException(__('User not found: %s', $userId));
        }

        return $row;
    }

    /**
     * Find by id.
     *
     * @param int $userId The ID
     *
     * @return array The row
     */
    public function findUserById(int $userId): array
    {
        return $this->fetchById('users', $userId);
    }

    /**
     * Update user.
     *
     * @param int $userId The user ID
     * @param array $data The user data
     *
     * @return bool Success
     */
    public function updateUser(int $userId, array $data): bool
    {
        if (empty($userId)) {
            throw new InvalidArgumentException('User ID required');
        }

        $this->newUpdate('users', $data)->andWhere(['id' => $data['id']])->execute();

        return true;
    }

    /**
     * Insert new user.
     *
     * @param array $data The user
     *
     * @return int The new ID
     */
    public function insertUser(array $data): int
    {
        return (int)$this->newInsert('users', $data)->execute()->lastInsertId();
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
