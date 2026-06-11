<?php
require 'db.php';

$stmt = $pdo->query('SELECT * FROM users ORDER BY created_at DESC');
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー一覧</title>
</head>
<body>
    <h1>ユーザー一覧</h1>
    <p><a href="create.php">新規登録</a></p>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>名前</th>
            <th>メール</th>
            <th>年齢</th>
            <th>登録日</th>
            <th>操作</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['id']) ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['age']) ?></td>
            <td><?= htmlspecialchars($user['created_at']) ?></td>
            <td>
                <a href="edit.php?id=<?= $user['id'] ?>">編集</a>
                <a href="delete.php?id=<?= $user['id'] ?>">削除</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>