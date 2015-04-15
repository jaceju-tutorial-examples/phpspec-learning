# PhpSpec Learning

```bash
composer require phpspec/phpspec
```

```bash
alias t=./vendor/bin/phpspec
```

[edit] `phpspec.yml`

```yaml
suites:
  main:
    namespace: KK
    psr4_prefix: KK
```

[edit] `composer.json`

```json
  "autoload": {
    "psr-4": {
      "KK\\": "src/"
    }
  }
```

```bash
composer dump-autoload
```

## Create specs of Playlist

```bash
t desc KK/Playlist
```

```bash
t run
```

```
Do you want me to create `KK\Playlist` for you? (y)
```

### First spec of Playlist

[edit] `spec/PlaylistSpec.php`

```php
use KK\Song;

    function it_add_a_song_to_playlist(Song $song)
    {
        $this->add($song);
        $this->shouldHaveCount(1);
    }
```

```bash
t run
```

```
Class spec\KK\Song does not exist
```

[edit] `src/Song.php`

```php
namespace KK;

class Song
{
}
```

```bash
t run
```

```
Do you want me to create `KK\Playlist::add()` for you? (y)
```

```
Do you want me to create `KK\Playlist::hasCount()` for you? (n)
```

[edit] `src/Playlist.php`

```php
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
```

```bash
t run
```

### Second spec of Playlist

[edit] `spec/PlaylistSpec.php`

```php
    function it_can_accept_multiple_songs_to_add_at_once(Song $song1, Song $song2)
    {
        $this->add([$song1, $song2]);
        $this->shouldHaveCount(2);
    }
```

```bash
t run
```

[edit] `src/Playlist.php`

```php
    public function add($song)
    {
        if (is_array($song)) {
            return array_map([$this, 'add'], $song);
        }

        $this->songs[] = $song;
    }
```

```bash
t run
```

## Introduce mock object

[edit] `spec/PlaylistSpec.php`

```php
    function it_can_mark_all_songs_as_played(Song $song1, Song $song2)
    {
        $song1->play()->shouldBeCalled();
        $song2->play()->shouldBeCalled();

        $this->add([$song1, $song2]);
        $this->markAllAsPlayed();
    }
```

```bash
t run
```

```
method `Double\KK\Song\P4::play()` is not defined.
```

[edit] `src/Song.php`

```php
    public function play()
    {
    }
```

```bash
t run
```

```
Do you want me to create `KK\Playlist::markAllAsPlayed()` for you? (y)
```

[edit] `src/Playlist.php`

```php
    public function markAllAsPlayed()
    {
        foreach ($this->songs as $song) {
            $song->play();
        }
    }
```

```bash
t run
```

## Create specs of Song

```
t desc KK/Song
```

[edit] `spec/SongSpec.php`

```php
    function it_can_be_stared()
    {
        $this->setStars(5);
        $this->getStars()->shouldBe(5);
    }
```

```bash
t run
```

```
Do you want me to create `KK\Song::setStars()` for you? (y)
```

```
Do you want me to create `KK\Song::getStars()` for you? (y)
```

[edit] `src/Song.php`

```php

class Song
{
    protected $stars;

    public function setStars($stars)
    {
        $this->stars = $stars;
    }

    public function getStars()
    {
        return $this->stars;
    }

    public function play()
    {
    }
}
```

### Spec for set invalid amount of stars

[edit] `spec/SongSpec.php`

```
    function its_stars_should_be_not_exceed_five()
    {
        $this->shouldThrow('InvalidArgumentException')->duringSetStars(8);
    }
```

```bash
t run
```

[edit] `src/Song.php`

```php
    public function setStars($stars)
    {
        if ($stars > 5) {
            throw new InvalidArgumentException;
        }

        $this->stars = $stars;
    }
```

### Refactoring

[edit] `src/Song.php`

```php
    public function setStars($stars)
    {
        $this->validateStarAmount($stars);

        $this->stars = $stars;
    }

    protected function validateStarAmount($stars)
    {
        if ($stars > 5) {
            throw new InvalidArgumentException;
        }
    }
```

## Spec of Song::play()

[edit] `spec/SongSpec.php`

```php
    function it_can_be_marked_as_played()
    {
        $this->play();
        $this->shouldBePlayed();
    }
```

```bash
t run
```

```
Do you want me to create `KK\Song::isPlayed()` for you?
```

[edit] `src/Song.php`

```php
    protected $played = false;

    public function play()
    {
        $this->played = true;
    }

    public function isPlayed()
    {
        return $this->played;
    }
```

## Pass argument to constructor in spec

[edit] `spec/SongSpec.php`

```php
    function it_can_fetch_the_name_of_the_song()
    {
        $this->getName()->shouldBe('La la la');
    }
```

```bash
t run
```

```
Do you want me to create `KK\Song::__construct()` for you? (y)
```

[edit] `src/Song.php`

```php
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
```

```bash
t run
```

[edit] `spec/SongSpec.php`

```
    function let()
    {
        $this->beConstructedWith('La la la');
    }
```

```bash
t run
```
