<?php

namespace App\Domain\User;

/**
 * Service.
 */
class UserService
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
     * @return User[]
     */
    public function findAllUsers(): array
    {
        $result = [];

        foreach ($this->userRepository->findAll() as $row) {
            $result[] = User::fromArray($row);
        }

        return $result;
    }

    /**
     * Get user by ID.
     *
     * @param int $userId The user ID
     *
     * @return User The data
     */
    public function getUserById(int $userId): User
    {
        $row = $this->userRepository->getById($userId);

        return User::fromArray($row);
    }

    /**
     * Register new user.
     *
     * @param User $user The user
     *
     * @return int New ID
     */
    public function registerUser(User $user): int
    {
        $row = [
            'username' => $user->getUsername(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'email' => $user->getEmail(),
            'locale' => $user->getLocale(),
            'password' => $user->getPassword(),
            'role' => $user->getRole(),
            'enabled' => $user->getEnabled() ? 1 : 0,
        ];

        return $this->userRepository->insertUser($row);
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
