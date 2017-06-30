<?php

/**
 * While Table Objects represent and provide access to a collection of objects,
 * entities represent individual rows or domain objects in your application.
 *
 * Entities are just value objects which contains no methods to manipulate the database.
 */

namespace App\Entity;

/**
 * Class UserEntity
 */
class UserEntity extends BaseEntity
{
    public $id;
    public $username;
    public $password;
    public $email;
    public $firstName;
    public $lastName;
    public $role;
    public $locale;
    public $disabled;
    public $created;
    public $createdBy;
    public $updated;
    public $updatedBy;
}
