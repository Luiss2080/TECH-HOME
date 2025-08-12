<?php

namespace Core;

class Header
{
    public  $title;
    public $subtitle;
    public    $date;

    public function __construct($title, $subtitle, $date)
    {
        $this->title    = $title;
        $this->subtitle = $subtitle;
        $this->date     = $date;
    }
}
