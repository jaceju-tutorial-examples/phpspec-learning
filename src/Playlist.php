<?php

namespace KK;

use Countable;

class Playlist implements Countable
{
    protected $songs;

    public function add($song)
    {
        $this->songs[] = $song;
    }

    public function count()
    {
        return count($this->songs);
    }
}
