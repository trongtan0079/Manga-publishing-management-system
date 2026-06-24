<?php

namespace App\Models;

class SeriesRanking
{
    public $id;
    public $series_id;
    public $views;
    public $rating;
    public $rank;
    public $updated_at;

    public function __construct() {
    }
}
