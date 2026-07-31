<?php
require 'functions.php';
require 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = '不正なリクエストです。';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit;
        } else {
            $errors[] = 'ユーザー名またはパスワードが正しくありません。';
        }
    }
}

$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>ログイン - 匿名掲示板</title>
</head>
<body>
<h1>ログイン</h1>

<?php foreach ($errors as $error): ?>
    <p style="color:red;"><?= e($error) ?></p>
<?php endforeach; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
    <label>ユーザー名: <input type="text" name="username" value="<?= e($_POST['username'] ?? '') ?>"></label><br>
    <label>パスワード: <input type="password" name="password"></label><br>
    <button type="submit">ログイン</button>
</form>

<p><a href="register.php">新規登録はこちら</a></p>
<p><a href="index.php">掲示板に戻る</a></p>
</body>
</html>
