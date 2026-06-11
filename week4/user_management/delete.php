<?php
require 'db.php';

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 実際に削除
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: index.php');
    exit;
}

// 確認のため対象ユーザーを取得
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー削除</title>
</head>
<body>
    <h1>ユーザー削除の確認</h1>
    <p>以下のユーザーを削除します。よろしいですか？</p>

    <ul>
        <li>名前: <?= htmlspecialchars($user['name']) ?></li>
        <li>メール: <?= htmlspecialchars($user['email']) ?></li>
        <li>年齢: <?= htmlspecialchars($user['age']) ?></li>
    </ul>

    <form method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
        <button type="submit">削除する</button>
        <a href="index.php">キャンセル</a>
    </form>
</body>
</html>