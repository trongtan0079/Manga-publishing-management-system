<?php

namespace App\Models;

class Notification
{
    public $id;
    public $user_id;
    public $message;
    public $is_read;
    public $created_at;

    public function __construct() {
    }
}
