<?php
require 'db.php';

// 並び替え（在庫数）の指定を受け取る。デフォルトは登録日の新しい順
$sort = $_GET['sort'] ?? 'new';
$category = $_GET['category'] ?? '';

// 並び替えの条件を決定（SQLに直接埋め込むのは固定値のみ）
switch ($sort) {
    case 'stock_asc':  $orderBy = 'stock ASC';      break;
    case 'stock_desc': $orderBy = 'stock DESC';     break;
    default:           $orderBy = 'created_at DESC'; break;
}

// カテゴリ一覧を取得（フィルタのプルダウン用）
$catStmt = $pdo->query('SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != "" ORDER BY category');
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

// 商品を取得（カテゴリ指定があれば絞り込み）
if ($category !== '') {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? ORDER BY $orderBy");
    $stmt->execute([$category]);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY $orderBy");
}
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>在庫管理システム</title>
</head>
<body>
    <h1>商品一覧</h1>
    <p><a href="create.php">新規商品を追加</a></p>

    <!-- フィルタ・並び替え -->
    <form method="get">
        カテゴリ:
        <select name="category">
            <option value="">すべて</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>

        並び替え:
        <select name="sort">
            <option value="new"        <?= $sort === 'new' ? 'selected' : '' ?>>登録日（新しい順）</option>
            <option value="stock_desc" <?= $sort === 'stock_desc' ? 'selected' : '' ?>>在庫数（多い順）</option>
            <option value="stock_asc"  <?= $sort === 'stock_asc' ? 'selected' : '' ?>>在庫数（少ない順）</option>
        </select>

        <button type="submit">適用</button>
    </form>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>商品名</th>
            <th>価格</th>
            <th>在庫数</th>
            <th>カテゴリ</th>
            <th>登録日</th>
            <th>操作</th>
        </tr>
        <?php foreach ($products as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['id']) ?></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars(number_format($p['price'], 2)) ?> 円</td>
            <td><?= htmlspecialchars($p['stock']) ?></td>
            <td><?= htmlspecialchars($p['category']) ?></td>
            <td><?= htmlspecialchars($p['created_at']) ?></td>
            <td>
                <a href="edit.php?id=<?= $p['id'] ?>">編集</a>
                <a href="delete.php?id=<?= $p['id'] ?>">削除</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>