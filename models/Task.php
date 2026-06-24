<?php

namespace App\Models;

class Task
{
    public $id;
    public $chapter_id;
    public $assigned_to;
    public $type;
    public $status;
    public $due_date;

    public function __construct() {
    }
}
