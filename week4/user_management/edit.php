<?php
require 'db.php';

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $age   = trim($_POST['age'] ?? '');

    if ($name === '')  $errors[] = '名前を入力してください';
    if ($email === '') $errors[] = 'メールを入力してください';

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'UPDATE users SET name = ?, email = ?, age = ? WHERE id = ?'
        );
        $stmt->execute([$name, $email, $age === '' ? null : $age, $id]);
        header('Location: index.php');
        exit;
    }
    $user = ['id' => $id, 'name' => $name, 'email' => $email, 'age' => $age];
} else {
    // 既存データを取得してフォームに表示
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    if (!$user) {
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー編集</title>
</head>
<body>
    <h1>ユーザー編集</h1>
    <p><a href="index.php">一覧に戻る</a></p>

    <?php if (!empty($errors)): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
        <p>名前: <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>"></p>
        <p>メール: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"></p>
        <p>年齢: <input type="number" name="age" value="<?= htmlspecialchars($user['age']) ?>"></p>
        <button type="submit">更新</button>
    </form>
</body>
</html>