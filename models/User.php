<?php

namespace App\Models;

class User
{
    public $id;
    public $username;
    public $password;
    public $email;
    public $role_id;
    public $created_at;
    public $updated_at;

    public function __construct() {
    }
}
