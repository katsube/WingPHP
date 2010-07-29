-----------------------------------------------------------------------------
phpunitディレクトリ内の取扱い説明書
-----------------------------------------------------------------------------
  ○これは何？
    ・PHPUnitを用いたユニットテストを行う際のファイルを置いておく
      ディレクトリです。

  ○インストール
    ・動作には PHPUnit が必要です。
    ・PHPUnitのインストール方法は以下です。

        # 配布されているチャンネルを追加
        $ pear channel-discover pear.phpunit.de
        $ pear channel-discover pear.symfony-project.com

        # PEARでインストール
        $ pear install phpunit/PHPUnit
      
      ※参照
        http://www.phpunit.de/manual/3.4/ja/installation.html

  ○テスト方法
    ・以下のように実行します。

        $ cd phpunit/model
        $ phpunit 
        PHPUnit 3.4.9 by Sebastian Bergmann.
        ..
        Time: 0 seconds, Memory: 4.00Mb

        OK (2 tests, 3 assertions)

    ・詳しい実行方法はPHPUnitのドキュメントを参照ください。
    ・model/BaseTest.php はファイル冒頭でDBへの接続設定を行って
      います。conf.phpとは別に設定する必要があります。

  ○その他
    ・PHPUnit公式
      http://www.phpunit.de/

    ・PHPUnit日本語ドキュメント(3.4)
      http://www.phpunit.de/manual/3.4/ja/automating-tests.html
