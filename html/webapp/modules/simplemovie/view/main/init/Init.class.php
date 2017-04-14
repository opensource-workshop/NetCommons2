<?php

/**
 * メイン表示処理
 *
 * @package     jp.opensource-workshop
 * @author      nagahara@opensource-workshop.jp
 * @copyright   2014 opensource-workshop.jp
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     Opensource-workshop NetCommons2 add-on module project
 * @access      public
 */
class Simplemovie_View_Main_Init extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;
	var $page_id = null;
	var $room_id = null;

	// 使用コンポーネントを受け取るため
	var $simplemovieView = null;

	// 値をセットするため

	// 現在の値
	var $block_record = null;
	
	// 埋め込みコード
	var $embed_code = null;
	
	/**
	 * メイン表示処理
	 *
	 * @access  public
	 */
	function execute()
	{

		// ブロックデータの取得
		$this->block_record = $this->simplemovieView->getBlock( $this->block_id );
		
		// ログインユーザーの権限を取得
		$login_user_auth = $this->simplemovieView->getLoginAuth();

		// 埋め込みタグデータ設定
		if( isset( $this->block_record['movie_upload_id'] ) )
		{
			// 動画がUploadされていて…
			// 埋め込みタグ表示フラグ＝1(全ての人に対して表示する) または
			// 埋め込みタグ表示フラグ＝2(主担以上の人に対して表示する)で、かつ、ログインユーザーの権限が主担以上の場合
			if( intval($this->block_record['embed_show_flag']) ===  1 ||
			   (intval($this->block_record['embed_show_flag']) ===  2 && $login_user_auth >= _AUTH_CHIEF ) )
			{
				$this->embed_code = 'src="' . BASE_URL . INDEX_FILE_NAME . '?action=simplemovie_view_main_iframe&movie_upload_id=' . $this->block_record['movie_upload_id'] . '" '
									. 'frameborder="0"  allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true"';
			}
		}

		//未承認かつ主担以上の場合は表示をリクエストに差し替え
		if($this->block_record['agree_flag'] == SIMPLEMOVIE_STATUS_RESERVE && $login_user_auth >= _AUTH_CHIEF)
		{
			$this->block_record['movie_upload_id'] = $this->block_record['movie_upload_id_request'];
			if( isset( $this->block_record['thumbnail_upload_id_request'] ) )
			{
				$this->block_record['thumbnail_upload_id'] = $this->block_record['thumbnail_upload_id_request'];
			}
		}
		return 'success';
	}
}
?>
