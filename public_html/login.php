
<?php
/**
 * login.php ― ログイン画面
 */
session_start();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
</head>
<body>
    <main>
        <h1>ログイン</h1>

        <?php if (isset($_SESSION['login_error'])): ?>
            <p style="color: red;">
                <?= htmlspecialchars($_SESSION['login_error'], ENT_QUOTES, 'UTF-8') ?>
            </p>
            <?php unset($_SESSION['login_error']); ?>
        <?php endif; ?>

        <form action="login-process.php" method="post">
            <p>
                <label>
                    メールアドレス<br>
                    <input type="email" name="email" required>
                </label>
            </p>

            <p>
                <label>
                    パスワード<br>
                    <input type="password" name="password" required>
                </label>
            </p>

            <button type="submit">ログイン</button>
        </form>
    </main>
</body>
</html>
