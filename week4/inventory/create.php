<?php
require 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $price    = trim($_POST['price'] ?? '');
    $stock    = trim($_POST['stock'] ?? '');
    $category = trim($_POST['category'] ?? '');

    // バリデーション
    if ($name === '')  $errors[] = '商品名を入力してください';
    if ($price === '') {
        $errors[] = '価格を入力してください';
    } elseif (!is_numeric($price) || $price < 0) {
        $errors[] = '価格は0以上の数値で入力してください';
    }
    if ($stock === '') {
        $stock = 0; // 在庫未入力は0扱い
    } elseif (!ctype_digit($stock)) {
        $errors[] = '在庫数は0以上の整数で入力してください';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'INSERT INTO products (name, price, stock, category) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$name, $price, $stock, $category === '' ? null : $category]);
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品追加</title>
</head>
<body>
    <h1>商品追加</h1>
    <p><a href="index.php">一覧に戻る</a></p>

    <?php if (!empty($errors)): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post">
        <p>商品名: <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"></p>
        <p>価格: <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>"> 円</p>
        <p>在庫数: <input type="number" name="stock" value="<?= htmlspecialchars($_POST['stock'] ?? '') ?>"></p>
        <p>カテゴリ: <input type="text" name="category" value="<?= htmlspecialchars($_POST['category'] ?? '') ?>"></p>
        <button type="submit">追加</button>
    </form>
</body>
</html>