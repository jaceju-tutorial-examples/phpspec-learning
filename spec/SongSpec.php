<?php

namespace spec\KK;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SongSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beAnInstanceOf('KK\Song');
    }

    function it_can_be_stared()
    {
        $this->setStars(5);
        $this->getStars()->shouldBe(5);
    }
}
