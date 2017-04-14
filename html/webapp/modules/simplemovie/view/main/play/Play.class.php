<?php

/**
 * 動画再生処理
 *
 * @package     jp.opensource-workshop
 * @author      nagahara@opensource-workshop.jp
 * @copyright   2014 opensource-workshop.jp
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     Opensource-workshop NetCommons2 add-on module project
 * @access      public
 */
class Simplemovie_View_Main_Play extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;
	var $page_id = null;
	var $room_id = null;
	var $play_block_id = null;

	// 使用コンポーネントを受け取るため
	var $simplemovieView = null;
	var $movieView = null;

	// 値をセットするため

	/**
	 * 動画再生処理
	 *
	 * @access  public
	 */
	function execute()
	{
// ★港区チェック用
//test_log(date('y/m/d H:i:s') . "Simplemovie_View_Main_Play:execute:[_REQUEST]" );
//test_log($_REQUEST);
		//test_log( $_SESSION );

		// ブロックデータの取得
		$block_record = $this->simplemovieView->getBlock( $this->play_block_id );

		//未承認かつ主担以上の場合は表示をリクエストに差し替え
		$login_user_auth = $this->simplemovieView->getLoginAuth();
		if($block_record['agree_flag'] == SIMPLEMOVIE_STATUS_RESERVE && $login_user_auth >= _AUTH_CHIEF)
		{
			$mUId = $block_record['movie_upload_id_request'];
		}else{
			$mUId = $block_record['movie_upload_id'];
		}
		//test_log( $this->movieView->test() );

		list( $pathname, $filename, $physical_file_name, $cache_flag ) = 
			$this->movieView->downloadCheck( $mUId );
// ★港区チェック用
//test_log(date('y/m/d H:i:s') . "Simplemovie_View_Main_Play:execute:[downloadCheck]Result" );
//test_log("pathname          [" . $pathname . "]");
//test_log("filename          [" . $filename . "]");
//test_log("physical_file_name[" . $physical_file_name . "]");
//test_log("cache_flag        [" . $cache_flag . "]");

		clearstatcache();
		if ( $pathname != null ) {
			$cache_flag = false;
			$this->movieView->headerOutput( $pathname, $filename, $physical_file_name, $cache_flag );
		}
		exit;

		//return 'success';
	}
}
?>
