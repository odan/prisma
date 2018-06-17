<?php

namespace App\Repository;

use App\Entity\UserEntity;
use DomainException;
use InvalidArgumentException;

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
        $rows = $this->fetchAll('users');

        $result = [];
        foreach ($rows as $row) {
            $result[] = new UserEntity($row);
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
     * @return UserEntity An entity
     */
    public function getById(int $id): UserEntity
    {
        $user = $this->findById($id);

        if (!$user) {
            throw new DomainException(__('User not found: %s', $id));
        }

        return $user;
    }

    /**
     * Find entity by id.
     *
     * @param int $id The ID
     *
     * @return UserEntity|null The entity
     */
    public function findById(int $id)
    {
        $row = $this->fetchById('users', $id);

        return $row ? new UserEntity($row) : null;
    }

    /**
     * Find user by username.
     *
     * @param string $username Username
     *
     * @return UserEntity|null User
     */
    public function findByUsername(string $username)
    {
        $query = $this->newSelect('users')->select('*');
        $query->andWhere([
            'username' => $username,
            'disabled' => 0,
        ]);

        $row = $query->execute()->fetch('assoc');

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
     * @return int User ID
     */
    public function saveUser(UserEntity $user): int
    {
        if ($user->id) {
            $this->updateUser($user);

            return $user->id;
        }

        return $this->insertUser($user);
    }

    /**
     * Update user.
     *
     * @param UserEntity $user The user
     *
     * @return bool Success
     */
    public function updateUser(UserEntity $user): bool
    {
        if (empty($user->id)) {
            throw new InvalidArgumentException('User ID required');
        }

        $this->newUpdate('users', $user->toArray())->andWhere(['id' => $user->id])->execute();

        return true;
    }

    /**
     * Insert new user.
     *
     * @param UserEntity $user The user
     *
     * @return int The new ID
     */
    public function insertUser(UserEntity $user): int
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
