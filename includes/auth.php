
<?php
/**
 * includes/auth.php ― ガード処理をまとめた共通ファイル（第9回・上級）
 *
 * require_login();  … ログインしている人だけ
 * require_admin();  … 管理者だけ
 */

// Session がまだ始まっていなければ開始する
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// admin フォルダにいる場合は ../ を付ける
function app_path(string $file): string
{
    $inAdmin = basename(dirname($_SERVER['SCRIPT_FILENAME'])) === 'admin';
    return ($inAdmin ? '../' : '') . $file;
}

// ① 認証：ログインしているか確認
function require_login(): void
{
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['login_error'] = '続けるにはログインが必要です';
        header('Location: ' . app_path('login.php'));
        exit;
    }
}

// ② 認可：管理者か確認
function require_admin(): void
{
    require_login();

    if (($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: ' . app_path('forbidden.php'));
        exit;
    }
}
