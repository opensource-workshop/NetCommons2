<?php

/**
 * 動画の表示設定
 *
 * @package     jp.opensource-workshop
 * @author      nagahara@opensource-workshop.jp
 * @copyright   2014 opensource-workshop.jp
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     Opensource-workshop NetCommons2 add-on module project
 * @access      public
 */
class Simplemovie_Action_Edit_Style extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;
	var $page_id = null;
	var $room_id = null;

	var $default_width   = null;		// 幅
	var $default_height  = null;		// 高さ
	var $autoplay_flag   = null;		// 自動再生フラグ
	var $embed_show_flag = null;		// 埋め込みタグ表示フラグ

	// 使用コンポーネントを受け取るため
	var $simplemovieView = null;
	var $simplemovieAction = null;

	// 値をセットするため

	/**
	 * 動画の表示設定
	 *
	 * @access  public
	 */
	function execute()
	{

		// 表示方法の変更
		$this->simplemovieAction->setStyle( array( 'block_id'        => $this->block_id,
												   'default_width'   => $this->default_width,
												   'default_height'  => $this->default_height,
												   'autoplay_flag'   => $this->autoplay_flag,
												   'embed_show_flag' => $this->embed_show_flag ) );
		return 'success';
	}
}
?>
