<?php
// 既習：DB接続・ログイン確認。HTML出力より前に実行する。
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();
$userId = (int) $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('POSTで送信してください');
}
if (
    !isset($_SESSION['csrf_token'], $_POST['csrf_token']) ||
    !is_string($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    exit('不正なリクエストです');
}
$itemId = filter_input(
    INPUT_POST,
    'item_id',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);
if ($itemId === false || $itemId === null) {
    http_response_code(400);
    exit('商品IDを確認してください');
}
try {
    $pdo->beginTransaction();
    $check = $pdo->prepare('SELECT stock FROM items WHERE id = ? FOR UPDATE');
    $check->execute([$itemId]);
    $item = $check->fetch();
    if (!$item) {
        $pdo->rollBack();
        http_response_code(404);
        exit('商品が見つかりません');
    }
    // 同じ商品が複数行ある既存のカートでも、合計数量で確認する。
    $checkCart = $pdo->prepare(
        'SELECT quantity FROM cart WHERE user_id = ? AND item_id = ? FOR UPDATE'
    );
    $checkCart->execute([$userId, $itemId]);
    $inCart = array_sum(array_column($checkCart->fetchAll(), 'quantity'));
    if ($inCart + 1 > (int) $item['stock']) {
        $pdo->rollBack();
        http_response_code(409);
        exit('在庫が不足しています。カートの数量を確認してください。');
    }
    $stmt = $pdo->prepare(
        'INSERT INTO cart (user_id, item_id, quantity) VALUES (?, ?, 1)'
    );
    $stmt->execute([$userId, $itemId]);
    $pdo->commit();
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    exit('カートへの追加に失敗しました。もう一度お試しください。');
}
$_SESSION['cart_message'] = 'カートに1個追加しました';
header('Location: item.php?id=' . $itemId);
exit;
