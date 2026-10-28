
<?php
/**
 * register-process.php ― 会員登録の処理
 */

// データベース接続
require_once __DIR__ . '/../includes/config.php';
session_start();

// POST以外のアクセスを禁止
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

// フォームから値を受け取る
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['password_confirm'] ?? '';

$errors = [];

// 必須チェック
if ($name === '') {
    $errors['name'] = '氏名を入力してください';
}

if ($email === '') {
    $errors['email'] = 'メールアドレスを入力してください';
}

if ($password === '') {
    $errors['password'] = 'パスワードを入力してください';
}

// メールアドレスの形式チェック
if ($email !== '' &&
    filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $errors['email'] = 'メールアドレスの形式が正しくありません';
}

/* パスワードの長さチェック */
if ($password !== '' && mb_strlen($password) < 8) {
    $errors['password'] =
        'パスワードは8文字以上で入力してください';
}

/* パスワード確認 */
if ($password !== $confirm) {
    $errors['password_confirm'] =
        'パスワード（確認）が一致しません';
}

/* メールアドレスの重複チェック */
if (!$errors) {
    $stmt = $pdo->prepare(
        'SELECT id FROM users WHERE email = ?'
    );

    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $errors['email'] =
            'このメールアドレスは既に登録されています';
    }
}


// エラーがあれば登録画面に戻す
if ($errors) {
    $_SESSION['register_errors'] = $errors;
    header('Location: register.php');
    exit;
}

// 登録：パスワードはハッシュ化してから保存する（元には戻せない）
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare(
    'INSERT INTO users (email, password, name) VALUES (?, ?, ?)'
);
$stmt->execute([$email, $hash, $name]);

// 登録完了画面へ移動
header('Location: register-done.php');
exit;
