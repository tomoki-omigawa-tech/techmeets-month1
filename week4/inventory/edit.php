<?php
require 'db.php';

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $price    = trim($_POST['price'] ?? '');
    $stock    = trim($_POST['stock'] ?? '');
    $category = trim($_POST['category'] ?? '');

    if ($name === '')  $errors[] = '商品名を入力してください';
    if ($price === '') {
        $errors[] = '価格を入力してください';
    } elseif (!is_numeric($price) || $price < 0) {
        $errors[] = '価格は0以上の数値で入力してください';
    }
    if ($stock === '') {
        $stock = 0;
    } elseif (!ctype_digit($stock)) {
        $errors[] = '在庫数は0以上の整数で入力してください';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'UPDATE products SET name = ?, price = ?, stock = ?, category = ? WHERE id = ?'
        );
        $stmt->execute([$name, $price, $stock, $category === '' ? null : $category, $id]);
        header('Location: index.php');
        exit;
    }
    $product = ['id' => $id, 'name' => $name, 'price' => $price, 'stock' => $stock, 'category' => $category];
} else {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if (!$product) {
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品編集</title>
</head>
<body>
    <h1>商品編集</h1>
    <p><a href="index.php">一覧に戻る</a></p>

    <?php if (!empty($errors)): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">
        <p>商品名: <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>"></p>
        <p>価格: <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ?>"> 円</p>
        <p>在庫数: <input type="number" name="stock" value="<?= htmlspecialchars($product['stock']) ?>"></p>
        <p>カテゴリ: <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>"></p>
        <button type="submit">更新</button>
    </form>
</body>
</html>