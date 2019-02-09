<?php

namespace App\Test\Fixture;

/**
 * Fixture.
 */
class UserFixture
{
    /**
     * @var string Table name
     */
    public $table = 'users';

    /**
     * Records.
     *
     * @var array Records
     */
    public $records = [
        [
            'id' => 1,
            'username' => 'admin',
            'password' => '$2y$10$8SCHkI4JUKJ2NA353BTHW.Kgi33HI.2C35xd/j5YUzBx05F1O4lJO',
            'email' => 'admin@example.com',
            'first_name' => null,
            'last_name' => null,
            'role' => 'ROLE_ADMIN',
            'locale' => 'en_US',
            'disabled' => 0,
            'created_at' => '2015-01-09 14:05:19',
            'created_by' => 1,
            'updated_at' => null,
            'updated_by' => null,
        ],
        [
            'id' => 2,
            'username' => 'user',
            'password' => '$1$X64.UA0.$kCSxRsj3GKk7Bwy3P6xn1.',
            'email' => 'user@example.com',
            'first_name' => null,
            'last_name' => null,
            'role' => 'ROLE_USER',
            'locale' => 'de_DE',
            'disabled' => 0,
            'created_at' => '2019-02-01 00:00:00',
            'created_by' => 1,
            'updated_at' => null,
            'updated_by' => null,
        ],
    ];
}
