<?php
require 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $age   = trim($_POST['age'] ?? '');

    // バリデーション
    if ($name === '')  $errors[] = '名前を入力してください';
    if ($email === '') $errors[] = 'メールを入力してください';

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'INSERT INTO users (name, email, age) VALUES (?, ?, ?)'
        );
        $stmt->execute([$name, $email, $age === '' ? null : $age]);
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー登録</title>
</head>
<body>
    <h1>ユーザー登録</h1>
    <p><a href="index.php">一覧に戻る</a></p>

    <?php if (!empty($errors)): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post">
        <p>名前: <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"></p>
        <p>メール: <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></p>
        <p>年齢: <input type="number" name="age" value="<?= htmlspecialchars($_POST['age'] ?? '') ?>"></p>
        <button type="submit">登録</button>
    </form>
</body>
</html>