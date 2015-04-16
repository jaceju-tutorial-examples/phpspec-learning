# phpspec Learning

* 如何開發 composer-based library ？
* 如何從預期的結果往回推出程式碼？
* 如何用更語義化的方式撰寫測試？

[phpspec](http://www.phpspec.net/en/latest/)

* SpecBDD 型測試框架 ([Behat](http://behat.org) 為 StoryBDD 型的測試框架)
* 先寫出 specification 再完成程式碼

## 初始化專案

建立一個專案資料夾：

```bash
mkdir kk-music && cd $_
```

引用 `phpspec` 套件：

```bash
composer require phpspec/phpspec
```

設定指令別名：

```bash
alias t=./vendor/bin/phpspec
```

* phpspec 會讀取專案根目錄下的 `phpspec.yml` 設定檔
* phpspec 可以針對不同 namespace 的類別建立規格檔案
* 可以有多組 suite

新增 `phpspec.yml` ：

```yaml
suites:
  main:
    namespace: KK
    psr4_prefix: KK
```

編輯 `composer.json` ：

```json
  "autoload": {
    "psr-4": {
      "KK\\": "src/"
    }
  }
```

```bash
composer dump
```

## 範例說明

主角：

* 播放清單 (Playlist)
* 歌曲 (Song)

播放清單規格：

* 可以加入單首歌曲
* 可以一次加入多首歌曲
* 可以將清單內所有歌曲設定為已播放

歌曲規格：

* 可以評價星數
* 不可加超過 5 的星
* 可以被設定為已播放
* 可以取得歌曲名稱

## 建立播放清單規格類別

用 `phpspec desc` 來建立規格：

```bash
t desc KK/Playlist
```

用 `phpspec run` 執行測試：

```bash
t run
```

phpspec 會詢問是否要建立對應的類別檔案：

```
Do you want me to create `KK\Playlist` for you? (y)
```

選 `y` 的話， phpspec 會自動幫我們建立對應的檔案。

### 規格一：可以加入單首歌曲

`Playlist` 類別會有一個 `add` 方法，接受一個 `Song` 物件來加到清單中。

* 在 `*Spec` 類別中的每個 `function` 都是一個規格，名稱要用完整的英文句字描述規格
* `$this` 在這裡會轉換身份，變成 `Playlist` 物件 (實際上不是)
* 因為測試的是 `Playlist` 類別的邏輯，所以要隔離 `Song` 類別

編輯 `spec/PlaylistSpec.php` ：

```php
use KK\Song;

class PlaylistSpec extends ObjectBehavior
{
    // ...

    function it_add_a_song_to_playlist(Song $song)
    {
        $this->add($song);
        $this->shouldHaveCount(1);
    }
}
```

* 還是要定義 `Song` 類別， phpspec 會自動以 type hint 來注入 Double 物件
* 這時的 `$song` 即為 Double 物件

執行測試：

```bash
t run
```

因為還沒有建立 `Song` 類別，所以 phpspec 會報錯。

```
Class spec\KK\Song does not exist
```

phpspec 無法自動產生 Double 物件的類別，需要自動建立。

新增 `src/Song.php`

```php
namespace KK;

class Song
{
}
```

再次執行：

```bash
t run
```

有了 `Song` 類別後，會繼續原來的流程：

```
Do you want me to create `KK\Playlist::add()` for you? (y)
```

phpspec 會自動幫我們建立對應的 `add` 方法。

```
Do you want me to create `KK\Playlist::hasCount()` for you? (n)
```

因為希望 `Playlist` 類別要實作 `Countable` 介面，所以不新增 `hasCount` 方法。

編輯 `src/Playlist.php` ：

```php
namespace KK;

use Countable;

class Playlist implements Countable
{
    protected $songs; // 內部用陣列來存放新增的歌曲

    public function add($song)
    {
        $this->songs[] = $song;
    }

    // Countable 介面需要實作 count 方法
    public function count()
    {
        return count($this->songs);
    }
}
```

再次執行測試：

```bash
t run
```

就應該會通過了第一個規格的測試。接著就可以將程式碼放入版本庫中，然後繼續實作第二個規格。

### 規格二：可以一次加入多首歌曲

規格的設計細節裡，我們希望 `add` 方法可以接受一個陣列，其中可包含一個以上的 `Song` 物件。

編輯 `spec/PlaylistSpec.php` ：

```php
    function it_can_accept_multiple_songs_to_add_at_once(Song $song1, Song $song2)
    {
        $this->add([$song1, $song2]);
        $this->shouldHaveCount(2);
    }
```

* Double 物件注入是依賴 type hint ，所以測試方法的參數就無法直接傳入陣列，必須一一指定
* `$song1` 與 `$song2` 為 Double 物件
* 但 `add` 方法的參數就可以把 `$song1` 與 `$song2` 包成陣列傳入

執行測試：

```bash
t run
```

會無法通過，所以要在 `Playlist::add()` 中加入新的程式碼。

編輯 `src/Playlist.php` ：

```php
    public function add($song)
    {
        if (is_array($song)) {
            return array_map([$this, 'add'], $song);
        }

        $this->songs[] = $song;
    }
```

* 利用 `array_map` 與 callback 來實作

執行測試：

```bash
t run
```

通過。

## 引入 Mock 物件

以 Double 物件隔離了非待測類別，但 Double 物件也有分成不同的類型。當 Double 物件有某方法被預期可能會被呼叫時，就變成了 Mock 物件 (或稱 Spy 物件) 。

* Mock 物件模擬了非待測類別的行為。
* Mock 物件可以用框架來自動產生。
* 參考：[Mock 物件](http://openhome.cc/Gossip/JUnit/MockObject.html)

編輯 `spec/PlaylistSpec.php` ：

```php
    function it_can_mark_all_songs_as_played(Song $song1, Song $song2)
    {
        $song1->play()->shouldBeCalled();
        $song2->play()->shouldBeCalled();

        $this->add([$song1, $song2]);
        $this->markAllAsPlayed();
    }
```

* 預期在 `Playlist::markAllAsPlayed()` 方法會呼叫到 `Song::play()` 方法
* 每個 `Song` 物件的 `play` 方法都要設為預期會被呼叫

執行測試：

```bash
t run
```

還沒有建立 `Song::play()` 方法時，會被 phpspec 報錯：

```
method `Double\KK\Song\P4::play()` is not defined.
```

* phpspec 無法自動建立 Mock 物件的方法

所以要手動加入 `play` 方法。

編輯 `src/Song.php` ：

```php
    public function play()
    {
    }
```

* 實際上 `play` 方法並不會真的被呼叫，所以只要建立一個空實作即可

執行測試：

```bash
t run
```

繼續流程：

```
Do you want me to create `KK\Playlist::markAllAsPlayed()` for you? (y)
```

然後實做 `Playlist::markAllAsPlayed()` 方法。

編輯 `src/Playlist.php` ：

```php
    public function markAllAsPlayed()
    {
        foreach ($this->songs as $song) {
            $song->play();
        }
    }
```

* 這裡的 `$song` 是 Mock 物件，所以 `play` 方法也是自動產生的

執行測試：

```bash
t run
```

通過。

## 建立歌曲規格類別

假設 `Playlist` 已經完成開發，但由於 `Song` 類別還沒有真正被實作，所以也需要再建立它的規格檔案：

```
t desc KK/Song
```

* 因為 `Song` 類別先前已經建立， phpspec 就不會再問

### 規格一：可以評價星數

每首歌曲都可以被評價其星數，可以用基本的 setter / getter 來實作。

編輯 `spec/SongSpec.php` ：

```php
    function it_can_be_stared()
    {
        $this->setStars(5);
        $this->getStars()->shouldBe(5);
    }
```

執行測試：

```bash
t run
```

依序詢問是否自動生成方法：

```
Do you want me to create `KK\Song::setStars()` for you? (y)
```

```
Do you want me to create `KK\Song::getStars()` for you? (y)
```

生成後就可以開始實作。

編輯 `src/Song.php` ：

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

執行測試：

```bash
t run
```

通過。

### 規格二：不可評價超過 5 的星數

前面的測試並沒有限制最高星數，所以該規格希望在星數超過 5 時要丟出異常。

編輯 `spec/SongSpec.php` ：

```
    function its_stars_should_be_not_exceed_five()
    {
        $this->shouldThrow('InvalidArgumentException')->duringSetStars(8);
    }
```

* `shouldThrow` 接受一個異常類別的名稱做為參數
* `duringSetStars` 會呼叫 `Song::setStars()` 方法

執行測試：

```bash
t run
```

確認會有失敗的狀況，就可以編寫程式碼。

編輯 `src/Song.php` ：

```php
    public function setStars($stars)
    {
        if ($stars > 5) {
            throw new InvalidArgumentException;
        }

        $this->stars = $stars;
    }
```

執行測試：

```bash
t run
```

通過。

### 重構

當完成測試後，可以先將程式碼提交到版本庫中，這時也可以再進行重構，讓程式碼具有可讀性。

編輯 `src/Song.php` ：

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

* 將產生異常的邏輯封裝在 `validateStarAmount` 方法中

執行測試：

```bash
t run
```

應該要通過，表示我們完成了重構。

## 規格三：可以被設定為已播放

前面的 `play` 方法還是空實作，所以要將它完成。當 `play` 方法被呼叫後，歌曲應為「已播放」的狀態。

編輯 `spec/SongSpec.php` ：

```php
    function it_can_be_marked_as_played()
    {
        $this->play();
        $this->shouldBePlayed();
    }
```

* `shouldBePlayed` 方法實際上不存在

執行測試：

```bash
t run
```

因為測試中呼叫了 `shouldBePlayed` 方法， phpspec 就會認為 `Song` 類別應該要有個 `isPlayed` 方法：

```
Do you want me to create `KK\Song::isPlayed()` for you? (y)
```

自動建立 `Song::isPlayed()` 方法後就可以實作。

編輯 `src/Song.php` ：

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

執行測試：

```bash
t run
```

通過。

## 規格四：可以取得歌曲名稱

有時候物件的屬性值是在 contruct 時初始化的，

編輯 `spec/SongSpec.php` ：

```php
    function it_can_fetch_the_name_of_the_song()
    {
        $this->getName()->shouldBe('La la la');
    }
```

執行測試：

```bash
t run
```

詢問是否建立對應的方法：

```
Do you want me to create `KK\Song::getName()` for you? (y)
```

這裡不再使用 setter ，而是改用 constructor

編輯 `src/Song.php` ：

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

執行測試：

```bash
t run
```

會產生以下的失敗訊息：

```
warning: Missing argument 1 for KK\Song::__construct()
```

我們需要讓 phpspec 協助我們做物件初始化時的參數注入。

編輯 `spec/SongSpec.php` ：

```
    function let()
    {
        $this->beConstructedWith('La la la');
    }
```

* `let` 方法會在 spec 類別的每個測試執行前被呼叫
* 在 `let` 方法中用 `beConstructedWith` 來注入參數

執行測試：

```bash
t run
```

通過。