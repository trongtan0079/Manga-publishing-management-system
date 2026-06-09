<?php

namespace App\Models;

class Page
{
    public $id;
    public $chapter_id;
    public $page_number;
    public $image_url;
    public $created_at;

    public function __construct() {
    }
}
