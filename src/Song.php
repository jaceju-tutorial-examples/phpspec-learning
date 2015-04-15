<?php

namespace KK;

use InvalidArgumentException;

class Song
{
    protected $stars;
    protected $listened = false;
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

    public function listen()
    {
        $this->listened = true;
    }

    protected function validateStarAmount($stars)
    {
        if ($stars > 5) {
            throw new InvalidArgumentException;
        }
    }

    public function isWatched()
    {
        return $this->listened;
    }

    public function getName()
    {
        return $this->name;
    }
}
