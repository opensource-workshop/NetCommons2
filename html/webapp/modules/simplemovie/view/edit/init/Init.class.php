<?php

/**
 * 編集画面　表示処理
 *
 * @package     jp.opensource-workshop
 * @author      nagahara@opensource-workshop.jp
 * @copyright   2014 opensource-workshop.jp
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     Opensource-workshop NetCommons2 add-on module project
 * @access      public
 */
class Simplemovie_View_Edit_Init extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;
	var $page_id = null;
	var $room_id = null;

	// 使用コンポーネントを受け取るため
	var $simplemovieView = null;

	// 値をセットするため

	// モジュールで設定されているアップロード・ファイルの最大サイズ
	var $simplemovie_upload_max_size_mb = null;

	// 現在の値
	var $block_record = null;

	// 必須フラグ
	var $movie_require_flag = null;
	var $thumbnail_require_flag = null;
	
	// 動画変換ボタン表示フラグ（1:表示する / 1以外:表示しない）
	var $change_execute_button_show = null;

	/**
	 * 編集画面　表示処理
	 *
	 * @access  public
	 */
	function execute()
	{

		// モジュールで設定されているアップロード・ファイルの最大サイズはバイトなので、MB にして画面に渡す。
		if ( ctype_digit( SIMPLEMOVIE_UPLOAD_MAX_SIZE_MEDIA ) ) {
			$this->simplemovie_upload_max_size_mb = floor( ( $this->simplemovie_upload_max_size_mb = SIMPLEMOVIE_UPLOAD_MAX_SIZE_MEDIA / 1024 / 1024 ) * 10) / 10;
		}

		// ブロックデータの取得
		$this->block_record = $this->simplemovieView->getBlock( $this->block_id );
		//自動承認不可者の場合は表示をリクエストに差し替え
		if($this->simplemovieView->isAutoAgree($this->simplemovieView->getAgreeFlag()) == false)
		{
			$this->block_record['movie_upload_id'] = $this->block_record['movie_upload_id_request'];
			$this->block_record['movie_file_name'] = $this->block_record['movie_file_name_request'];
			$this->block_record['thumbnail_upload_id'] = $this->block_record['thumbnail_upload_id_request'];
			$this->block_record['thumbnail_file_name'] = $this->block_record['thumbnail_file_name_request'];
		}
		//print_r($this->block_record);

		// 必須フラグ
		$this->movie_require_flag = SIMPLEMOVIE_REQUIRE_MOVIE;
		$this->thumbnail_require_flag = SIMPLEMOVIE_REQUIRE_THUMBNAIL;

		// 動画変換ボタン表示フラグを取得して設定
		if ( ctype_digit( SIMPLEMOVIE_CHANGE_EXECUTE_BUTTON_SHOW ) && intval( SIMPLEMOVIE_CHANGE_EXECUTE_BUTTON_SHOW ) === 1 )
		{
			$this->change_execute_button_show = 1;
		}

		return 'success';
	}
}
?>
