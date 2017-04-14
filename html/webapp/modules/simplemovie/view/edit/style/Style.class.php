<?php

/**
 * 表示方法画面　表示処理
 *
 * @package     jp.opensource-workshop
 * @author      nagahara@opensource-workshop.jp
 * @copyright   2014 opensource-workshop.jp
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     Opensource-workshop NetCommons2 add-on module project
 * @access      public
 */
class Simplemovie_View_Edit_Style extends Action
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

	/**
	 * 表示方法画面　表示処理
	 *
	 * @access  public
	 */
	function execute()
	{

		// ブロックデータの取得
		$this->block_record = $this->simplemovieView->getBlock( $this->block_id );

		return 'success';
	}
}
?>
