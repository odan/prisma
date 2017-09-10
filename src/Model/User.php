<?php

namespace App\Model;

/**
 * User model
 */
class User extends BaseModel
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
    public $createdAt;
    public $createdBy;
    public $updatedAt;
    public $updatedBy;
}
