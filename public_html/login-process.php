
<?php
/**
 * login-process.php ― ログイン処理
 */
require_once __DIR__ . '/../includes/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$genericError = 'メールアドレスまたはパスワードが違います';

// ① メールアドレスでユーザーを探す
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

// ② いる かつ パスワード一致 → 成功
if ($user && password_verify($password, $user['password'])) {
    // ログイン失敗回数をリセット
    $reset = $pdo->prepare('UPDATE users SET failed_count = 0, locked_until = NULL WHERE id = ?');
    $reset->execute([$user['id']]);

    // セッションにユーザー情報を保存
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['role'] = $user['role'];

    header('Location: mypage.php');
    exit;
}