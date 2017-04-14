<?php

/**
 * サムネイル表示処理
 *
 * @package     jp.opensource-workshop
 * @author      nagahara@opensource-workshop.jp
 * @copyright   2014 opensource-workshop.jp
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     Opensource-workshop NetCommons2 add-on module project
 * @access      public
 */
class Simplemovie_View_Main_Thumbnail extends Action
{
	// リクエストパラメータを受け取るため
	var $upload_id = null;
	var $thumbnail_flag = null;
	var $w = null;
	var $h = null;

	// 使用コンポーネントを受け取るため
	var $uploadsView = null;

	/**
	 * サムネイル表示
	 *
	 * @access  public
	 */
	function execute()
	{
		if( $this->w != null || $this->h != null ) {
			$resize = array( $this->w, $this->h );
		}
		else {
			$resize = null;
		}
		list($pathname,$filename,$physical_file_name, $cache_flag) = $this->uploadsView->downloadCheck($this->upload_id, null, $this->thumbnail_flag, 'simplemovie_view_main_play', $resize);
		clearstatcache();
		if($pathname != null) {
			$this->uploadsView->headerOutput($pathname, $filename, $physical_file_name, $cache_flag);
		}

		//return 'success';
	}
}
?>
