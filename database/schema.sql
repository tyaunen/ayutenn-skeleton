-- ユーザーテーブル
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` varchar(16) NOT NULL COMMENT 'ユーザーID',
  `user_name` varchar(32) NOT NULL COMMENT 'ユーザー名',
  `password_hash` varchar(255) NOT NULL COMMENT 'パスワードハッシュ',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='ユーザー管理テーブル';

-- セッションテーブル（必要な場合）
-- CREATE TABLE IF NOT EXISTS `sessions` ( ... );
