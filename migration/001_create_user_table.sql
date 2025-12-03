-- ユーザーテーブル作成
CREATE TABLE IF NOT EXISTS `user` (
    `user_id` VARCHAR(16) NOT NULL PRIMARY KEY COMMENT 'ユーザーID',
    `user_name` VARCHAR(50) NOT NULL COMMENT 'ユーザー名',
    `password` VARCHAR(255) NOT NULL COMMENT 'パスワード(ハッシュ化)',
    `profile` TEXT DEFAULT '' COMMENT 'プロフィール',
    `last_login` DATETIME DEFAULT NULL COMMENT '最終ログイン日時',
    `on_create` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
    `on_update` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
    `is_deleted` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '論理削除フラグ(0:有効, 1:削除)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='ユーザー';

-- インデックス作成
CREATE INDEX idx_user_on_create ON `user`(`on_create`);
CREATE INDEX idx_user_is_deleted ON `user`(`is_deleted`);
