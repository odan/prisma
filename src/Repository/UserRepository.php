<?php

namespace App\Repository;

use App\Data\UserData;
use DomainException;
use InvalidArgumentException;

/**
 * Users repository.
 */
final class UserRepository extends ApplicationRepository
{
    /**
     * Find all users.
     *
     * @return UserData[]
     */
    public function findAll(): array
    {
        $rows = $this->fetchAll('users');

        $result = [];
        foreach ($rows as $row) {
            $result[] = new UserData($row);
        }

        return $result;
    }

    /**
     * Get user by id.
     *
     * @param int $id User id
     *
     * @throws DomainException On error
     *
     * @return UserData An model
     */
    public function getById(int $id): UserData
    {
        $user = $this->findById($id);

        if (!$user) {
            throw new DomainException(__('User not found: %s', $id));
        }

        return $user;
    }

    /**
     * Find by id.
     *
     * @param int $id The ID
     *
     * @return UserData|null The model
     */
    public function findById(int $id): ?UserData
    {
        $row = $this->fetchById('users', $id);

        return $row ? new UserData($row) : null;
    }

    /**
     * Insert or update user.
     *
     * @param UserData $user
     *
     * @return int User ID
     */
    public function saveUser(UserData $user): int
    {
        if ($user->getId() !== null) {
            $this->updateUser($user);

            return $user->getId();
        }

        return $this->insertUser($user);
    }

    /**
     * Update user.
     *
     * @param UserData $user The user
     *
     * @return bool Success
     */
    public function updateUser(UserData $user): bool
    {
        if (empty($user->getId())) {
            throw new InvalidArgumentException('User ID required');
        }

        $this->newUpdate('users', $user->toArray())->andWhere(['id' => $user->getId()])->execute();

        return true;
    }

    /**
     * Insert new user.
     *
     * @param UserData $user The user
     *
     * @return int The new ID
     */
    public function insertUser(UserData $user): int
    {
        return (int)$this->newInsert('users', $user->toArray())->execute()->lastInsertId();
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
