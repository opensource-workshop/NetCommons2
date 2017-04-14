<?php

/**
 * 動画のアップロード処理
 *
 * @package     jp.opensource-workshop
 * @author      nagahara@opensource-workshop.jp
 * @copyright   2014 opensource-workshop.jp
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     Opensource-workshop NetCommons2 add-on module project
 * @access      public
 */
class Simplemovie_Action_Edit_Init extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;
	var $page_id = null;
	var $room_id = null;

	// 動画の削除チェック
	var $movie_delete = null;

	// サムネイルの削除チェック
	var $thumbnail_delete = null;

	// アップロード・ファイル情報
	var $upload_files = null;

	// 使用コンポーネントを受け取るため
	var $simplemovieView = null;
	var $simplemovieAction = null;
	var $uploadsAction = null;

	// 値をセットするため

	/**
	 * 動画のアップロード処理
	 *
	 * @access  public
	 */
	function execute()
	{

		// 削除処理
		if ( !empty( $this->movie_delete ) || !empty( $this->thumbnail_delete ) ) {
			$this->simplemovieAction->deleteUploads( $this->block_id, $this->movie_delete, $this->thumbnail_delete );
		}

		// アップロード・ファイル情報の登録
		if ( !empty( $this->upload_files ) ) {
			$this->simplemovieAction->setUploads( $this->block_id, $this->upload_files );
		}

		return 'success';
	}
}
?>
