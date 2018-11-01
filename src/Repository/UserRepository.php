<?php

namespace App\Repository;

use App\Model\UserModel;
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
     * @return UserModel[]
     */
    public function findAll(): array
    {
        $rows = $this->fetchAll('users');

        $result = [];
        foreach ($rows as $row) {
            $result[] = new UserModel($row);
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
     * @return UserModel An model
     */
    public function getById(int $id): UserModel
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
     * @return UserModel|null The model
     */
    public function findById(int $id): ?UserModel
    {
        $row = $this->fetchById('users', $id);

        return $row ? new UserModel($row) : null;
    }

    /**
     * Find user by username.
     *
     * @param string $username Username
     *
     * @return UserModel|null User
     */
    public function findByUsername(string $username): ?UserModel
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

        return new UserModel($row);
    }

    /**
     * Insert or update user.
     *
     * @param UserModel $user
     *
     * @return int User ID
     */
    public function saveUser(UserModel $user): int
    {
        if ($user->getId()) {
            $this->updateUser($user);

            return $user->getId();
        }

        return $this->insertUser($user);
    }

    /**
     * Update user.
     *
     * @param UserModel $user The user
     *
     * @return bool Success
     */
    public function updateUser(UserModel $user): bool
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
     * @param UserModel $user The user
     *
     * @return int The new ID
     */
    public function insertUser(UserModel $user): int
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
