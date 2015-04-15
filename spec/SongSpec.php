<?php

namespace spec\KK;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SongSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('La la la');
    }

    function it_can_be_stared()
    {
        $this->setStars(5);
        $this->getStars()->shouldBe(5);
    }

    function its_stars_should_be_not_exceed_five()
    {
        $this->shouldThrow('InvalidArgumentException')->duringSetStars(8);
    }

    function it_can_be_marked_as_listened()
    {
        $this->listen();
        $this->shouldBeWatched();
    }

    function it_can_fetch_the_name_of_the_song()
    {
        $this->getName()->shouldBe('La la la');
    }
}
