<?php

/**
 * ダウンロード表示クラス(主担以上のルーム権限だとダウンロード可能)
 *
 * @package     jp.opensource-workshop
 * @author      makino@opensource-workshop.jp
 * @copyright   2016 opensource-workshop.jp
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     Opensource-workshop NetCommons2 add-on module project
 * @access      public
 */
class Simplemovie_View_Edit_Download extends Action
{
	// リクエストパラメータを受け取るため
	var $upload_id = null;
	
	// 使用コンポーネントを受け取るため
	var $uploadsView = null;
	
	
	/**
	 * ダウンロードメイン表示クラス
	 *
	 * @access  public
	 */
	function execute()
	{
		// タイムリミットを設定
		set_time_limit(SIMPLEMOVIE_TIME_LIMIT);
		
		// 対象ファイル情報取得（権限がないと取得できない）
		list( $pathname, $filename, $physical_file_name, $cache_flag ) = $this->uploadsView->downloadCheck( $this->upload_id, null, 0, "simplemovie_view_main_play", null );
		
		// ファイルのステータスのキャッシュをクリアする
		clearstatcache();
		
		// 値がnull以外の場合（ちゃんとデータが取得できている場合）
		if( $pathname != null )
		{
			$this->uploadsView->headerOutput( $pathname, $filename, $physical_file_name, $cache_flag );
		}
	}
}
?>
