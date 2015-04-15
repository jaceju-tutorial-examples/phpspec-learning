<?php

namespace KK;

use InvalidArgumentException;

class Song
{
    protected $stars;
    protected $played = false;
    protected $name;

    public function __construct($title)
    {
        $this->name = $title;
    }

    public function setStars($stars)
    {
        $this->validateStarAmount($stars);

        $this->stars = $stars;
    }

    public function getStars()
    {
        return $this->stars;
    }

    public function play()
    {
        $this->played = true;
    }

    protected function validateStarAmount($stars)
    {
        if ($stars > 5) {
            throw new InvalidArgumentException;
        }
    }

    public function isPlayed()
    {
        return $this->played;
    }

    public function getName()
    {
        return $this->name;
    }
}
