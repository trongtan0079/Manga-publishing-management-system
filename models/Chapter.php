<?php

namespace App\Models;

class Chapter
{
    public $id;
    public $series_id;
    public $title;
    public $chapter_number;
    public $status;
    public $created_at;

    public function __construct() {
    }
}
