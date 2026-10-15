
<?php
/**
 * logout.php ― ログアウト
 */
session_start();

// ① セッションの中身を空にする
$_SESSION = [];

// ② セッションCookieを削除する
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path']
    );
}

// ③ サーバー側のセッションを破棄する
session_destroy();

// ログイン画面へ戻る
header('Location: login.php');
exit;
