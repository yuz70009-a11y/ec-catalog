
<?php
/**
 * register.php ― 会員登録の画面
 * 验证和注册处理交给 register-process.php
 */
session_start();

$errors = $_SESSION['register_errors'] ?? [];
unset($_SESSION['register_errors']);
$old = $_SESSION['register_old'] ?? [];
unset($_SESSION['register_old']);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>会員登録 ― Tsuchi-to-Hi</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <a href="index.php">Tsuchi-to-Hi</a>
    </header>

    <main>
        <h1>会員登録</h1>

       <?php if ($errors): ?>
    <div class="error">
        入力内容に <?= count($errors) ?> 件のエラーがあります。
        赤い文字の項目を直してください。
    </div>
<?php endif; ?>

        <form action="register-process.php" method="post">
            <p>
                <label>氏名<br>
                    <input type="text" name="name"
       value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

<?php if (isset($errors['name'])): ?>
    <small style="color:red">
        <?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?>
    </small>
<?php endif; ?>
                </label>
            </p>

            <p>
                <label>メールアドレス<br>
                    <input type="email" name="email"
       value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

<?php if (isset($errors['email'])): ?>
    <small style="color:red">
        <?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?>
    </small>
<?php endif; ?>
                </label>
            </p>

            
<p>
    <label>パスワード（8文字以上）<br>
        <input type="password" name="password" required>
    </label>

    <?php if (isset($errors['password'])): ?>
        <small style="color:red">
            <?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?>
        </small>
    <?php endif; ?>
</p>

<p>
    <label>パスワード（確認）<br>
        <input type="password" name="password_confirm" required>
    </label>

    <?php if (isset($errors['password_confirm'])): ?>
        <small style="color:red">
            <?= htmlspecialchars($errors['password_confirm'], ENT_QUOTES, 'UTF-8') ?>
        </small>
    <?php endif; ?>
</p>


            <button type="submit">登録する</button>
        </form>

        <p><a href="login.php">登録済みの方はログイン</a></p>
    </main>
</body>
</html>
