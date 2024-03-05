<?php
error_reporting(0);  //エラー非表示
?>

<?php
session_start();  //セッションを使うために必須で、セッションの開始を表す。
$mode = 'input';  //ページ遷移についてinput,confirm,sendの3つのモードを作成。これらを変数$modeで管理する。初期はinput。
$errmessage = array();  //えらメッセージのクリア(初期化)
if (isset($_POST['back']) && $_POST['back']) {  //backという配列のキーが存在している且つ、POSTに値が入っているかを確認。
    // 何もしない
} else if (isset($_POST['confirm']) && $_POST['confirm']) {
    // 確認画面
    // 名前
    if (!$_POST['fullname']) {
        $errmessage[] = "名前を入力してください";
    } else if (mb_strlen($_POST['fullname']) > 100) {  //「mb_strlen」関数で、引数に指定した文字列の長さを取得
        $errmessage[] = "名前は100文字以内にしてください";
    }
    $_SESSION['fullname'] = htmlspecialchars($_POST['fullname'], ENT_QUOTES);  //セッションに値を保存する（配列もOK）。htmlspecialchars関数を$_POSTに適用。

    //メールアドレス
    if (!$_POST['email']) {
        $errmessage[] = "メールアドレスを入力してください";
    } else if (mb_strlen($_POST['email']) > 200) {
        $errmessage[] = "メールアドレスは200文字以内にしてください";
    } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errmessage[] = "メールアドレスが不正です";
    }
    $_SESSION['email']    = htmlspecialchars($_POST['email'], ENT_QUOTES);

    //問い合わせ
    if (!$_POST['message']) {
        $errmessage[] = "お問い合わせ内容を入力してください";
    } else if (mb_strlen($_POST['message']) > 500) {
        $errmessage[] = "お問い合わせ内容は500文字以内にしてください";
    }
    $_SESSION['message'] = htmlspecialchars($_POST['message'], ENT_QUOTES);

    //エラーメッセージ
    if ($errmessage) {
        $mode = 'input';  //エラーがあれば入力画面モード
    } else {
        $mode = 'confirm';  //無ければ確認画面モード
    }
} else if (isset($_POST['send']) && $_POST['send']) {
    // 送信ボタンを押したとき
    $message  = "お問い合わせを受け付けました \r\n"
        . "名前: " . $_SESSION['fullname'] . "\r\n"
        . "email: " . $_SESSION['email'] . "\r\n"
        . "お問い合わせ内容:\r\n"
        . preg_replace("/\r\n|\r|\n/", "\r\n", $_SESSION['message']);  //preg_replace()関数は、改行のコードを揃えるために使うもので、\r\n\、\r、\nをそれぞれ全て\r\nに変換しています。
    mail($_SESSION['email'], 'お問い合わせありがとうございます', $message);
    mail('pikanatuyasumi@yahoo.co.jp', 'お問い合わせ通知', $message);
    $_SESSION = array();  //セッション情報をクリア（初期化する）
    $mode = 'send';  //送信画面モード
} else {  //GETで来た時用にセッションを初期化。
    $_SESSION['fullname'] = "";
    $_SESSION['email']    = "";
    $_SESSION['message']  = "";
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせフォーム</title>
    <link rel="stylesheet" href="portfolio.css">
</head>

<body>
    <div class="container">
        <header>
            <div class="header_title">
                <a href="portfolio.html">HikaruKode</a>
            </div>

            <ul class="header_nav" id="js-nav">
                <li><a href="portfolio.html#about">About</a></li>
                <li><a href="portfolio.html#skill">Skill</a></li>
                <li><a href="portfolio.html#works">Works</a></li>
                <li><a href="portfolio.html#contact">Contact</a></li>
            </ul>

            <button class="header_hamburger" id="js-hamburger">
                <span class="sp1" id="js-sp1"></span>
                <span class="sp2" id="js-sp2"></span>
                <span class="sp3" id="js-sp3"></span>
            </button>

        </header>

        <main class="main">

            <?php if ($mode == "input") { ?>
                <!-- エラーメッセージ -->
                <?php
                if ($errmessage) {
                    echo '<div style="color:red; text-align:center; margin-top:5vh;">';
                    echo implode('<br>', $errmessage);
                    echo '</div>';
                }
                ?>
                <!-- 入力画面 -->
                <div class="form-wrapper">
                <form action="" method="post">
                    <div>
                        <label for="fullname">お名前</label><br>
                        <input type="text" id="fullname" name="fullname" value="<?php echo $_SESSION["fullname"] ?>" class="form-control" />
                    </div>
                    <div>
                        <label for="email">メールアドレス</label><br>
                        <input type="email" id="email" name="email" value="<?php echo $_SESSION["email"] ?>" />
                    </div>
                    <div>
                        <label for="message">お問い合わせ内容</label><br>
                        <textarea id="message" name="message" cols="" rows="" class="form-control"><?php echo $_SESSION["message"] ?></textarea>
                    </div>
                    <div>
                        <input type="submit" name="confirm" value="確認" class="form-button" />
                    </div>
                    
                </form>
                </div>
            <?php } else if ($mode == "confirm") { ?>
                <!-- 確認画面 -->
                <form action="" method="post">
                    <div>
                        <label for="fullname">お名前</label><br>
                        <?php echo $_SESSION["fullname"] ?>
                    </div>

                    <div>
                        <label for="email">メールアドレス</label><br>
                        <?php echo $_SESSION["email"] ?>
                    </div>

                    <div>
                        <label for="message">お問い合わせ内容</label><br>
                        <?php echo nl2br($_SESSION["message"]) ?>
                    </div>
                    <div>
                        <input type="submit" name="back" value="戻る" class="form-button" />
                        <input type="submit" name="send" value="送信" class="form-button" />
                    </div>
                </form>
            <?php } else { ?>
                <div>
                    <p class="form-p">送信しました。</p>
                    <div class="home-button">
                        <a href="portfolio.html"><button class="form-button">ホームに戻る</button></a>
                    </div>

                </div>

            <?php } ?>
        </main>

        <div class="pagetop">
            <a href="#">▲TOP</a>
        </div>

        <footer class="footer">
        </footer>

        <script src="portfolio.js"></script>
    </div>
</body>

</html>


