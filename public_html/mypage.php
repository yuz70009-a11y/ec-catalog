
<?php
/**
 * mypage.php ― マイページ
 */
require_once __DIR__ . '/../includes/config.php';
session_start();

$name = 'ゲスト';
$role = '';

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare(
        'SELECT name, role FROM users WHERE id = ?'
    );
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if ($user) {
        $name = $user['name'];
        $role = $user['role'];
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>マイページ</title>
</head>
<body>
    <main>
        <h1>マイページ</h1>

        <p>
            ようこそ、
            <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>
            さん
        </p>

        <?php if ($role !== ''): ?>
            <p>
                会員種別：
                <?= $role === 'admin' ? '管理者' : '一般会員' ?>
            </p>
        <?php endif; ?>

        <p><a href="logout.php">ログアウト</a></p>
    </main>
</body>
</html>

