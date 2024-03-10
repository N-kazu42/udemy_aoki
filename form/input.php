<?php
session_start();

require 'validation.php';

header('X-FRAME-OPTIONS:DENY');

// if (!empty($_POST)) {
//     echo '<pre>';
//     var_dump($_POST);
//     echo '</pre>';
// }
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// 入力、確認、完了　input.php,confirm.php,thanks.php
// CSRF 偽物のinput.php
// input.php


$pageFlag = 0;

$errors = validation($_POST);
// if (!empty($_POST)) {
//     echo '<pre>';
//     var_dump($errors);
//     echo '</pre>';
// }
if (!empty($_POST['btn_confirm']) && empty($errors)) {
    $pageFlag = 1;
}
if (!empty($_POST['btn_submit'])) {
    $pageFlag = 2;
}

function hiddenInput($name, $value)
{
    echo '<input type="hidden" name="' . $name . '" value="' . $value . '">';
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">

                <?php if ($pageFlag === 0) : ?>
                    <?php
                    if (!isset($_SESSION['csrfToken'])) {
                        $csrfToken = bin2hex(random_bytes(32));
                        $_SESSION['csrfToken'] = $csrfToken;
                    }
                    $token = $_SESSION['csrfToken'];
                    ?>

                    <?php if (!empty($errors) && !empty($_POST['btn_confirm'])) : ?>
                        <?php echo `<ul>`; ?>
                        <?php
                        foreach ($errors as $error) {
                            echo '<li>' . $error . '</li>';
                        }
                        ?>
                        <?php echo `</ul>`; ?>

                    <?php endif; ?>

                    <form method="POST" action="input.php">
                        <div class="form-group">
                            <label for="your_name">氏名</label>
                            <input type="text" class="form-control" id="your_name" name="your_name" value="<?php if (!empty($_POST['your_name'])) {
                                                                                                                echo h($_POST['your_name']);
                                                                                                            } ?>" require>



                        </div>
                        <div class="form-group">
                            <label for="email">メールアドレス</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php if (!empty($_POST['email'])) {
                                                                                                        echo h($_POST['email']);
                                                                                                    } ?>" require>
                        </div>
                        <div class="form-group">
                            <label for="url">ホームページ</label>
                            <input type="url" class="form-control" id="url" name="url" value="<?php if (!empty($_POST['url'])) {
                                                                                                    echo h($_POST['url']);
                                                                                                } ?>" require>
                        </div>

                        性別
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender1" value="0" <?php
                                                                                                                if (isset($_POST['gender']) && $_POST['gender'] === '0') {
                                                                                                                    echo 'checked';
                                                                                                                }

                                                                                                                ?>>
                            <label class="form-check-label" for="gender1">男性</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender2" value="1" <?php
                                                                                                                if (isset($_POST['gender']) && $_POST['gender'] === '1') {
                                                                                                                    echo 'checked';
                                                                                                                }

                                                                                                                ?>>
                            <label class="form-check-label" for="gender2">女性</label>
                        </div>

                        <div class="form-floating">
                            <select class="form-select" id="age" aria-label="Floating label select example">
                                <option value="">選択してください</option>
                                <?php
                                for ($i = 1; $i <= 6; $i++) {
                                    $minAge = ($i - 1) * 10 + 1;
                                    $maxAge = $i * 10;
                                    $selected = isset($_POST['age']) && $_POST['age'] == $i ? 'selected' : '';

                                    if ($i == 6) {
                                        // 最後のオプションの場合、表示を"60歳以上"にします
                                        echo "<option value=\"$i\" $selected>60歳以上</option>";
                                    } else {
                                        // それ以外の場合、範囲を表示します
                                        echo "<option value=\"$i\" $selected>${minAge}歳〜${maxAge}歳</option>";
                                    }
                                }
                                ?>
                            </select>
                            <label for="age">年齢</label>
                        </div>

                        <div class="mb-3">
                            <label for="contact" class="form-label">お問い合わせ内容</label>
                            <textarea class="form-control" name="contact" id="contact" rows="3"><?php if (!empty($_POST['contact'])) {
                                                                                                    echo h($_POST['contact']);
                                                                                                } ?></textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="caution">
                            <label class="form-check-label" for="caution">
                            注意事項にチェック
                            </label>
                        </div>

                        <input type="submit" name="btn_confirm" class="btn btn-info" value="確認する">
                        <input type="hidden" name="csrf" value="<?php echo $token; ?>">
                    </form>
                <?php endif; ?>

                <?php if ($pageFlag === 1) : ?>
                    <?php if ($_POST['csrf'] === $_SESSION['csrfToken']) : ?>
                        <form method="POST" action="input.php">
                            氏名 <?php echo h($_POST['your_name']); ?>
                            <br>
                            メールアドレス
                            <?php echo h($_POST['email']); ?>
                            <br>
                            ホームページ
                            <?php echo h($_POST['url']); ?>
                            <br>
                            性別
                            <?php
                            if ($_POST['gender'] === '0') {
                                echo '男性';
                            }
                            if ($_POST['gender'] === '1') {
                                echo '女性';
                            }
                            ?>
                            <br>
                            年齢
                            <?php
                            $intAge = (int)$_POST['age'];
                            echo ($intAge - 1) * 10 + 1 . '-' . $intAge * 10 . '歳';
                            ?>
                            <br>
                            お問い合わせ内容
                            <?php echo h($_POST['contact']); ?>

                            <br>
                            <input type="submit" name="btn_back" value="戻る">
                            <input type="submit" name="btn_submit" value="送信する">
                            <?php
                            hiddenInput('your_name', h($_POST['your_name']));
                            hiddenInput('email', h($_POST['email']));
                            hiddenInput('url', h($_POST['url']));
                            hiddenInput('gender', h($_POST['gender']));
                            hiddenInput('age', h($_POST['age']));
                            hiddenInput('contact', h($_POST['contact']));
                            hiddenInput('csrf', h($_POST['csrf']));
                            ?>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($pageFlag === 2) : ?>
                    <?php if ($_POST['csrf'] === $_SESSION['csrfToken']) : ?>

                        送信が完了しました
                        <?php unset($_SESSION['csrfToken']); ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-fbbOQedDUMZZ5KreZpsbe1LCZPVmfTnH7ois6mU1QK+m14rQ1l2bGBq41eYeM/fS" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>