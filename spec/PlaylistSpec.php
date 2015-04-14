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

    function it_can_accept_multiple_songs_to_add_at_once(Song $song1, Song $song2)
    {
        $this->add([$song1, $song2]);
        $this->shouldHaveCount(2);
    }
}
