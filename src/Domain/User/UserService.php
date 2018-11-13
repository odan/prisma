<?php

namespace App\Domain\User;

use App\Data\UserData;
use App\Repository\UserRepository;
use App\Domain\ApplicationService;

/**
 * Class.
 */
class UserService extends ApplicationService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Constructor.
     *
     * @param UserRepository $userRepository The repository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Find all users.
     *
     * @return UserData[]
     */
    public function findAllUsers(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * Get user by ID.
     *
     * @param int $userId The user ID
     *
     * @return UserData The data
     */
    public function getUserById(int $userId): UserData
    {
        return $this->userRepository->getById($userId);
    }

    /**
     * Register new user.
     *
     * @param UserData $user The user
     *
     * @return int New ID
     */
    public function registerUser(UserData $user): int
    {
        return $this->userRepository->insertUser($user);
    }

    /**
     * Register new user.
     *
     * @param int $userId The user ID
     *
     * @return bool Success
     */
    public function unregisterUser(int $userId): bool
    {
        return $this->userRepository->deleteUser($userId);
    }
}
