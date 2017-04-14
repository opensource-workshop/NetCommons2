<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * アップデートクラス
 *
 * @package     jp.opensource-workshop
 * @author      makino@opensource-workshop.jp
 * @copyright   2014 opensource-workshop.jp
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     Opensource-workshop NetCommons2 add-on module project
 * @access      public
 */
class Simplemovie_Update extends Action
{
	//使用コンポーネントを受け取るため
	var $db = null;

    /**
     * execute実行
     *
     * @access  public
     */
	function execute()
	{
		$adodb = $this->db->getAdoDbObject();

		// simplemovieテーブルに、embed_show_flag(埋め込みコード表示フラグ)がなければ追加する
		$metaColumns = $adodb->MetaColumns($this->db->getPrefix()."simplemovie");
		if (!isset($metaColumns["embed_show_flag"]) && !isset($metaColumns["EMBED_SHOW_FLAG"]))
		{
			$sql = "ALTER TABLE `".$this->db->getPrefix()."simplemovie` ADD `embed_show_flag` TINYINT UNSIGNED NOT NULL DEFAULT 2 AFTER `autoplay_flag`;";
			$result = $this->db->execute($sql);
			if ($result === false) {
				return false;
			}
		}
		else
		{
			// simplemovieテーブルに、embed_show_flag(埋め込みコード表示フラグ)がある場合は、デフォルト値を"2"に変更する
			// ※デフォルト値が既に"2"でも ALTER TABLE を実行する
			$sql = "ALTER TABLE `".$this->db->getPrefix()."simplemovie` ALTER `embed_show_flag` SET DEFAULT 2;";
			$result = $this->db->execute($sql);
			if ($result === false) {
				return false;
			}
		}

		//承認用カラムを追加する
		if (!isset($metaColumns["agree_flag"]) && !isset($metaColumns["AGREE_FLAG"]))
		{
			$sql = "ALTER TABLE `".$this->db->getPrefix()."simplemovie` ADD `agree_flag` int(11) NOT NULL DEFAULT '2' AFTER `embed_show_flag`;";
			$result = $this->db->execute($sql);
			if ($result === false) {
				return false;
			}
			//既に登録されているデータは1:承認済とする
			$result = $this->db->execute("UPDATE `".$this->db->getPrefix()."simplemovie` SET `agree_flag` = 1");
			if ($result === false) {
				return false;
			}
		}

		//承認待ち動画カラムを追加する
		if (!isset($metaColumns["movie_upload_id_request"]) && !isset($metaColumns["MOVIE_UPLOAD_ID_REQUEST"]))
		{
			$sql = "ALTER TABLE `".$this->db->getPrefix()."simplemovie` ADD `movie_upload_id_request` int(11) unsigned DEFAULT NULL AFTER `movie_upload_id`;";
			$result = $this->db->execute($sql);
			if ($result === false) {
				return false;
			}
		}
		//承認待ちサムネイルカラムを追加する
		if (!isset($metaColumns["thumbnail_upload_id_request"]) && !isset($metaColumns["THUMBNAIL_UPLOAD_ID_REQUEST"]))
		{
			$sql = "ALTER TABLE `".$this->db->getPrefix()."simplemovie` ADD `thumbnail_upload_id_request` int(11) unsigned DEFAULT NULL AFTER `thumbnail_upload_id`;";
			$result = $this->db->execute($sql);
			if ($result === false) {
				return false;
			}
		}

		return true;
	}
}
?>
