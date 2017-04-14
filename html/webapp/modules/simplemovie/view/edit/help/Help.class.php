<?php

/**
 * ヘルプ画面（使い方説明）の表示
 *
 * @package     jp.opensource-workshop
 * @author      nagahara@opensource-workshop.jp
 * @copyright   2012 opensource-workshop.jp
 * @license     Opensource-workshop Commercial License
 * @project     Opensource-workshop add-on module project
 * @access      public
 */
class Simplemovie_View_Edit_Help extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;
	var $page_id = null;
	var $room_id = null;

	// 使用コンポーネントを受け取るため
	var $simplemovieView = null;
	
	// 動画変換ボタン表示フラグ（1:表示する / 1以外:表示しない）
	var $change_execute_button_show = null;

	/**
	 * メイン処理
	 *
	 * @access  public
	 */
	function execute()
	{
		// 動画変換ボタン表示フラグを取得して設定
		if ( ctype_digit( SIMPLEMOVIE_CHANGE_EXECUTE_BUTTON_SHOW ) && intval( SIMPLEMOVIE_CHANGE_EXECUTE_BUTTON_SHOW ) === 1 )
		{
			$this->change_execute_button_show = 1;
		}

		return 'success';
	}
}
?>
