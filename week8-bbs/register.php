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

        if ($username === '' || mb_strlen($username) > 50) {
            $errors[] = 'ユーザー名を正しく入力してください（50文字以内）。';
        }
        if (mb_strlen($password) < 8) {
            $errors[] = 'パスワードは8文字以上で入力してください。';
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $errors[] = 'そのユーザー名は既に使われています。';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
                $stmt->execute([$username, $hash]);
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $username;
                header('Location: index.php');
                exit;
            }
        }
    }
}

$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>新規登録 - 匿名掲示板</title>
</head>
<body>
<h1>新規登録</h1>

<?php foreach ($errors as $error): ?>
    <p style="color:red;"><?= e($error) ?></p>
<?php endforeach; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
    <label>ユーザー名: <input type="text" name="username" value="<?= e($_POST['username'] ?? '') ?>"></label><br>
    <label>パスワード: <input type="password" name="password"></label><br>
    <button type="submit">登録する</button>
</form>

<p><a href="login.php">ログインはこちら</a></p>
<p><a href="index.php">掲示板に戻る</a></p>
</body>
</html>
