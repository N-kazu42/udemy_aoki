<?php
session_start();

require 'validation.php';

header('X-FRAME-OPTIONS:DENY');

if (!empty($_POST)) {
    echo '<pre>';
    var_dump($_POST);
    echo '</pre>';
}
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// 入力、確認、完了　input.php,confirm.php,thanks.php
// CSRF 偽物のinput.php
// input.php


$pageFlag = 0;

$errors = validation($_POST);
if (!empty($_POST)) {
    echo '<pre>';
    var_dump($errors);
    echo '</pre>';
}
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
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php if ($pageFlag === 0) : ?>
        <?php
        if (!isset($_SESSION['csrfToken'])) {
            $csrfToken = bin2hex(random_bytes(32));
            $_SESSION['csrfToken'] = $csrfToken;
        }
        $token = $_SESSION['csrfToken'];
        ?>

        <?php if(!empty($errors) && !empty($_POST['btn_confirm'])) :?>
            <?php echo `<ul>` ;?>
            <?php
                foreach($errors as $error){
                    echo '<li>' . $error . '</li>';
                }
                ?>
            <?php echo `</ul>` ;?>
            
        <?php endif; ?>
        <form method="POST" action="input.php">
            氏名 <input type="text" name="your_name" value="<?php if (!empty($_POST['your_name'])) {
                                                                echo h($_POST['your_name']);
                                                            } ?>">
            <br>
            メールアドレス <input type="email" name="email" value="<?php if (!empty($_POST['email'])) {
                                                                echo h($_POST['email']);
                                                            } ?>">
            <br>
            ホームページ <input type="url" name="url" value="<?php if (!empty($_POST['url'])) {
                                                            echo h($_POST['url']);
                                                        } ?>">
            <br>
            性別 <input type="radio" name="gender" value="0" <?php
                                                            if (isset($_POST['gender']) && $_POST['gender'] === '0') {
                                                                echo 'checked';
                                                            }

                                                            ?>>男性
            <input type="radio" name="gender" value="1" <?php
                                                        if (isset($_POST['gender']) && $_POST['gender'] === '1') {
                                                            echo 'checked';
                                                        }

                                                        ?>>女性
            <br>
            年齢
            <select name="age">
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

            <br>
            お問い合わせ内容
            <textarea name="contact"><?php if (!empty($_POST['contact'])) {
                                            echo h($_POST['contact']);
                                        } ?></textarea>
            <br>
            <input type="checkbox" name="caution" value="1">注意事項にチェック
            <br>

            <input type="submit" name="btn_confirm" value="確認する">
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
</body>

</html>