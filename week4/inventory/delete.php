<?php
require 'db.php';

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品削除</title>
</head>
<body>
    <h1>商品削除の確認</h1>
    <p>以下の商品を削除します。よろしいですか？</p>

    <ul>
        <li>商品名: <?= htmlspecialchars($product['name']) ?></li>
        <li>価格: <?= htmlspecialchars(number_format($product['price'], 2)) ?> 円</li>
        <li>在庫数: <?= htmlspecialchars($product['stock']) ?></li>
        <li>カテゴリ: <?= htmlspecialchars($product['category']) ?></li>
    </ul>

    <form method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">
        <button type="submit">削除する</button>
        <a href="index.php">キャンセル</a>
    </form>
</body>
</html>