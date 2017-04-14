<?php

/**
 * シンプル動画承認処理
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Simplemovie_Action_Main_Agree extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;
	var $agree_flag = null;

	// 使用コンポーネントを受け取るため
	var $db = null;
	var $mailMain = null;

	// 値をセットするため

	function execute()
	{
		// 承認処理
		if ( $this->agree_flag == 1 ) {
			// 一緒にページ情報も取得する。
			$params = array( "block_id" => $this->block_id );

			$sql = "SELECT a.*, p.page_id, p.page_name, p.permalink ".
					"FROM {simplemovie} a, {blocks} b ".
					  "LEFT JOIN {pages} p ON b.page_id = p.page_id ".
					"WHERE a.block_id = ? AND a.block_id = b.block_id";

			$simplemovie = $this->db->execute($sql, $params);
			if ($simplemovie === false) {
				$this->db->addError();
				return $simplemovie;
			}

			$params = array(
				"agree_flag" => SIMPLEMOVIE_STATUS_AGREE,
				"movie_upload_id" => $simplemovie[0]["movie_upload_id_request"],
				"thumbnail_upload_id" => $simplemovie[0]["thumbnail_upload_id_request"],
				"movie_upload_id_request" => null,
				"thumbnail_upload_id_request" => null
			);
			$result = $this->db->updateExecute("simplemovie", $params, array("block_id"=>$this->block_id), true);
			if ($result === false) {
				return 'error';
			}

			// メールで指定するURL の編集
			$page_url = BASE_URL;
			if ( !empty( $simplemovie[0]['permalink'] ) ) {
				$page_url .= "/" . $simplemovie[0]['permalink'];
			} else {
				$page_url .= "/?page_id=" . $simplemovie[0]['page_id'];
			}

			// メール本文
			$mail_body  = "以下のページ内のシンプル動画が承認されました。\n\n";
			$mail_body .= "ページタイトル：" . $simplemovie[0]['page_name'] . "\n";
			$mail_body .= "URL　　　　　 ：" . $page_url . "\n";

			// メール送信ユーザ
			$sql = "SELECT u.user_id, u.role_authority_id, mail.content AS email, u.login_id, u.handle ".
					"FROM {users} u ".
					"INNER JOIN {users_items_link} mail ON u.user_id = mail.user_id ".
						"AND mail.item_id = ( SELECT item_id FROM {items} WHERE item_name = 'USER_ITEM_EMAIL' ) ".
					"WHERE u.user_id = ? ".
						"AND mail.content != ''";
			$params = array( "update_user_id" => $simplemovie[0]["update_user_id"] );
			
			$send_user = $this->db->execute( $sql, $params );
			if ( $send_user === false ) {
				$this->db->addError();
				return false;
			}

			$this->mailMain->setSubject("シンプル動画が承認されました。");
			$this->mailMain->setBody($mail_body);
			$this->mailMain->setToUsers($send_user);
			$this->mailMain->send();
		}

		return 'success';
	}
}
?>
