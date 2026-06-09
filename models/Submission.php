<?php

namespace App\Models;

class Submission
{
    public $id;
    public $task_id;
    public $file_url;
    public $submitted_by;
    public $submitted_at;

    public function __construct() {
    }
}
