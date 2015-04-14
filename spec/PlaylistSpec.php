<?php

namespace spec\KK;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use KK\Song;

class PlaylistSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('KK\Playlist');
    }

    function it_add_a_song_to_playlist(Song $song)
    {
        $this->add($song);
        $this->shouldHaveCount(1);
    }
}
