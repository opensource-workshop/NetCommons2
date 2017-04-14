<?php

/**
 * ブロック削除アクション
 *
 * @package     jp.opensource-workshop
 * @author      nagahara@opensource-workshop.jp
 * @copyright   2014 opensource-workshop.jp
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     Opensource-workshop NetCommons2 add-on module project
 * @access      public
 */
class Simplemovie_Action_Edit_Delblock extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;

	// 使用コンポーネントを受け取るため
	var $simplemovieView = null;
	var $simplemovieAction = null;
	var $db = null;
	var $uploadsAction = null;

	// 値をセットするため

	// Filter_Whatsnewに値をセットするため
	// var $whatsnew = array();

	function execute()
	{

		// ブロックデータを取得
		$simplemovie = $this->simplemovieView->getBlock( $this->block_id );

		if ( empty( $simplemovie ) || count( $simplemovie ) == 0 ) {
			return 'success';
		}

		// ファイル削除処理
		$mUId = $simplemovie['movie_upload_id'];
		if ( empty( $mUId ) == false ) $this->simplemovieAction->deleteUpload($mUId);
			$tUId = $simplemovie['thumbnail_upload_id'];
		if ( empty( $tUId ) == false ) $this->simplemovieAction->deleteUpload($tUId);
			$mUIdR = $simplemovie['movie_upload_id_request'];
		if ( empty( $mUIdR ) == false ) $this->simplemovieAction->deleteUpload($mUIdR);
			$tUIdR = $simplemovie['thumbnail_upload_id_request'];
		if ( empty( $tUIdR ) == false ) $this->simplemovieAction->deleteUpload($tUIdR);

		// ブロックデータ削除処理
		$result = $this->db->deleteExecute( "simplemovie", array( "block_id" => $this->block_id ) );
		if ($result === false) {
			return 'error';
		}

		//--新着情報関連 Start--
		//$this->whatsnew = array(
		//	"unique_id" => $this->block_id
		//);
		//--新着情報関連 End--

		return 'success';
	}
}
?>
