
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

$cartId = filter_input(
    INPUT_POST,
    'cart_id',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

$quantity = filter_input(
    INPUT_POST,
    'quantity',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1, 'max_range' => 2147483647]]
);

if (
    $cartId === false || $cartId === null ||
    $quantity === false || $quantity === null
) {
    http_response_code(400);
    exit;
}


try {
    $find = $pdo->prepare('SELECT item_id FROM cart WHERE id = ? AND user_id = ?');
    $find->execute([$cartId, $userId]);
    $itemId = $find->fetchColumn();
    if ($itemId === false) {
        http_response_code(404);
        exit('カートの商品が見つかりません。');
    }
    $pdo->beginTransaction();
    $stockQuery = $pdo->prepare('SELECT stock FROM items WHERE id = ? FOR UPDATE');
    $stockQuery->execute([$itemId]);
    $stock = $stockQuery->fetchColumn();
    $rows = $pdo->prepare(
        'SELECT id, quantity FROM cart WHERE user_id = ? AND item_id = ? FOR UPDATE'
    );
    $rows->execute([$userId, $itemId]);
    $otherQuantity = 0;
    $found = false;
    foreach ($rows->fetchAll() as $row) {
        if ((int) $row['id'] === $cartId) {
            $found = true;
        } else {
            $otherQuantity += (int) $row['quantity'];
        }
    }
    if (!$found || $stock === false) {
        $pdo->rollBack();
        http_response_code(404);
        exit('カートの商品が見つかりません。');
    }
    if ($quantity + $otherQuantity > (int) $stock) {
        $pdo->rollBack();
        http_response_code(409);
        exit('在庫が不足しています。数量を減らしてから更新してください。');
    }
    $stmt = $pdo->prepare('UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?');
    $stmt->execute([$quantity, $cartId, $userId]);
    $pdo->commit();
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    exit('数量の更新に失敗しました。もう一度お試しください。');
}

header('Location: cart.php');
exit;
