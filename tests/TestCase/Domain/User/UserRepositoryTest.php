<?php

namespace App\Test\TestCase\Domain\User;

use App\Domain\User\UserRepository;
use App\Test\TestCase\DbTestCase;
use App\Test\Fixture\UserFixture;

/**
 * Tests.
 *
 * @coversDefaultClass \App\Domain\User\UserRepository
 */
class UserRepositoryTest extends DbTestCase
{
    /**
     * Create repository.
     *
     * @return UserRepository the repository
     */
    protected function createRepository(): UserRepository
    {
        return new UserRepository($this->getConnection());
    }

    /**
     * Fixtures.
     *
     * @var array
     */
    public $fixtures = [
        UserFixture::class,
    ];

    /**
     * Test.
     *
     * @covers ::findAll
     *
     * @return void
     */
    public function testFindAll(): void
    {
        $repository = $this->createRepository();
        $actual = $repository->findAll();

        $this->assertNotEmpty($actual);
    }

    /**
     * Test.
     *
     * @covers ::findUserById
     *
     * @return void
     */
    public function testFindUserById(): void
    {
        $repository = $this->createRepository();
        $actual = $repository->findUserById(1);

        $fixture = new UserFixture();
        $expected = $fixture->records[0];

        $this->assertEquals($expected, $actual);
    }
}
