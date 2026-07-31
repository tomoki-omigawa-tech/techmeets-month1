<?php
require 'functions.php';
require 'db.php';

$errors = [];

// 投稿処理（ログイン必須）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin();

    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = '不正なリクエストです。';
    } else {
        $body = trim($_POST['body'] ?? '');

        if ($body === '') {
            $errors[] = '本文を入力してください。';
        } elseif (mb_strlen($body) > 1000) {
            $errors[] = '本文は1000文字以内で入力してください。';
        } else {
            $stmt = $pdo->prepare('INSERT INTO posts (user_id, body) VALUES (?, ?)');
            $stmt->execute([$_SESSION['user_id'], $body]);
            header('Location: index.php');
            exit;
        }
    }
}

$posts = $pdo->query('
    SELECT posts.*, users.username
    FROM posts
    JOIN users ON posts.user_id = users.id
    ORDER BY posts.created_at DESC
')->fetchAll();

$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>匿名掲示板</title>
</head>
<body>
<h1>掲示板</h1>

<?php if (isLoggedIn()): ?>
    <p>
        ようこそ、<?= e($_SESSION['username']) ?> さん
        ／ <a href="logout.php">ログアウト</a>
    </p>

    <?php foreach ($errors as $error): ?>
        <p style="color:red;"><?= e($error) ?></p>
    <?php endforeach; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <textarea name="body" rows="4" cols="50"></textarea><br>
        <button type="submit">投稿する</button>
    </form>
<?php else: ?>
    <p>
        <a href="login.php">ログイン</a> または
        <a href="register.php">新規登録</a> すると投稿できます。
    </p>
<?php endif; ?>

<hr>

<?php if (empty($posts)): ?>
    <p>まだ投稿がありません。</p>
<?php else: ?>
    <?php foreach ($posts as $post): ?>
        <div style="border:1px solid #ccc; padding:8px; margin-bottom:8px;">
            <strong><?= e($post['username']) ?></strong>
            <small>（<?= e($post['created_at']) ?>）</small>
            <p><?= nl2br(e($post['body'])) ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
