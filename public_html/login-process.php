
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

// ② ユーザーが存在し、パスワードが一致した場合
if ($user && $password === $user['password']) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];

    header('Location: mypage.php');
    exit;
}

// ③ ログイン失敗
$_SESSION['login_error'] = $genericError;
header('Location: login.php');
exit;
