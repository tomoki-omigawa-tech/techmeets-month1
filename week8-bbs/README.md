# Week 8 練習課題1: ログイン機能付き掲示板

匿名で誰でも投稿できた掲示板に、認証機能（新規登録・ログイン・ログアウト）を追加した練習課題です。

## 機能一覧

- ユーザー登録・ログイン・ログアウト（PHPネイティブのセッション管理）
- ログインユーザーのみ投稿可能
- 投稿に投稿者名が表示される
- 未ログインでも投稿一覧の閲覧は可能

## 使用技術

- PHP 8.5（内蔵サーバー使用、Dockerなし）
- SQLite（`bbs.sqlite`、ファイル1つで完結）
- PDO（プリペアドステートメント使用）

## セキュリティ対策

- **SQLインジェクション対策**: PDOのプリペアドステートメントを使用
- **XSS対策**: 出力時に `htmlspecialchars()` でエスケープ
- **CSRF対策**: 全POSTフォームにトークンを埋め込み、`hash_equals()` で検証
- **パスワード保存**: `password_hash()` によるbcryptハッシュ化（平文保存なし）

これらのテスト結果は、`laravel-docker-app` リポジトリの `docs/security-report.pdf` にまとめています。

## セットアップ

```bash
cd week8-bbs
php -S localhost:8081
```

ブラウザで http://localhost:8081 にアクセス

### 必要なPHP拡張

- `pdo_sqlite`
- `mbstring`

`php.ini` でこれらが無効化されている場合は、該当行の `;` を削除して有効化してください。

## ファイル構成

- `db.php` - SQLite接続・テーブル初期化
- `functions.php` - セッション・CSRFトークン・認証系の共通関数
- `register.php` - 新規登録
- `login.php` - ログイン
- `logout.php` - ログアウト
- `index.php` - 掲示板本体（投稿一覧・投稿フォーム）
