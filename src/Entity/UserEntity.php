<?php

namespace App\Entity;

/**
 * User entity.
 */
class UserEntity extends AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $username;

    /** @var string */
    public $password;

    /** @var string */
    public $email;

    /** @var string */
    public $firstName;

    /** @var string */
    public $lastName;

    /** @var string */
    public $role;

    /** @var string */
    public $locale;

    /** @var bool */
    public $disabled;

    /** @var string */
    public $createdAt;

    /** @var int */
    public $createdBy;

    /** @var string */
    public $updatedAt;

    /** @var int */
    public $updatedBy;
}
