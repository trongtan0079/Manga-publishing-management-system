<?php

namespace App\Models;

class Review
{
    public $id;
    public $submission_id;
    public $reviewer_id;
    public $comments;
    public $status;
    public $reviewed_at;

    public function __construct() {
    }
}
