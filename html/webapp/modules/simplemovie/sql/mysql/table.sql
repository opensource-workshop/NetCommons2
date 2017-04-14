-- 
-- テーブルの構造 `simplemovie`
-- 
-- 使用するテーブルのCreate文をここに記述
CREATE TABLE `simplemovie` (
  `block_id` int(10) unsigned NOT NULL,
  `movie_upload_id` int(11) unsigned DEFAULT NULL,
  `movie_upload_id_request` int(11) unsigned DEFAULT NULL,
  `thumbnail_upload_id` int(11) unsigned DEFAULT NULL,
  `thumbnail_upload_id_request` int(11) unsigned DEFAULT NULL,
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `autoplay_flag`        tinyint unsigned NOT NULL default 0,
  `embed_show_flag`      tinyint unsigned NOT NULL default 2,
  `agree_flag`       int(11) NOT NULL DEFAULT '2',
  `room_id` int(11) NOT NULL DEFAULT '0',
  `insert_time` varchar(14) NOT NULL DEFAULT '',
  `insert_site_id` varchar(40) NOT NULL DEFAULT '',
  `insert_user_id` varchar(40) NOT NULL DEFAULT '',
  `insert_user_name` varchar(255) NOT NULL,
  `update_time` varchar(14) NOT NULL DEFAULT '',
  `update_site_id` varchar(40) NOT NULL DEFAULT '',
  `update_user_id` varchar(40) NOT NULL DEFAULT '',
  `update_user_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`block_id`)
) ENGINE=MyISAM;
