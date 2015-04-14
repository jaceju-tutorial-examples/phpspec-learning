<?php

namespace KK;

use InvalidArgumentException;

class Song
{
    protected $stars;

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
    }

    protected function validateStarAmount($stars)
    {
        if ($stars > 5) {
            throw new InvalidArgumentException;
        }
    }
}
