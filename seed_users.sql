-- ============================================================
-- seed_users.sql ― ログイン動作確認用のテストアカウント
--
-- 会員登録フォーム（第10回で実装）がまだ無いため、動作確認用に
-- 直接INSERTしています。パスワードはまだ平文です（第11回でハッシュ化）。
-- ============================================================
USE ec_shop;

INSERT INTO users (email, password, name, address, role) VALUES
('taro@example.com', 'taro1234', '山田 太郎', '東京都新宿区1-1-1', 'member'),
('admin@example.com', 'adminpass', '管理者', '東京都新宿区1-1-1', 'admin');
