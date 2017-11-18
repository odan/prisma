<?php

namespace App\Repository;

/**
 * Repositories The Right Way
 *
 * Implement separate database logic functions for all your needs inside
 * the specific repositories, so your service classes/controllers end up looking like this:
 *
 * $user = $userRepository->findByUsername('admin');
 * $users = $userRepository->findAdminIdsCreatedBeforeDate('2016-01-18 19:21:20');
 * $posts = $postRepository->chunkFilledPostsBeforeDate('2016-01-18 19:21:20');
 *
 * This way all the database logic is moved to the specific repository and I can type hint
 * it's returned models. This methodology also results in cleaner easier to read
 * code and further separates your core logic from the ORM / query builder.
 */
abstract class AbstractRepository implements RepositoryInterface
{
}
