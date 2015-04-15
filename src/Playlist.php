<?php

namespace KK;

use Countable;

class Playlist implements Countable
{
    protected $songs;

    public function add($song)
    {
        if (is_array($song)) {
            return array_map([$this, 'add'], $song);
        }

        $this->songs[] = $song;
    }

    public function count()
    {
        return count($this->songs);
    }

    public function markAllAsPlayed()
    {
        foreach ($this->songs as $song) {
            $song->play();
        }
    }
}
