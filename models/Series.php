<?php

namespace App\Models;

class Series
{
    public $id;
    public $title;
    public $description;
    public $mangaka_id;
    public $status;
    public $created_at;

    public function __construct() {
    }
}
