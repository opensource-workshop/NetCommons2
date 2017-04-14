<?php

/**
 * iframe内動画再生表示処理
 *
 * @package     jp.opensource-workshop
 * @author      makino@opensource-workshop.jp
 * @copyright   2014 opensource-workshop.jp
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     Opensource-workshop NetCommons2 add-on module project
 * @access      public
 */
class Simplemovie_View_Main_Iframe extends Action
{
	// リクエストパラメータを受け取るため
	var $movie_upload_id = null;		// 動画UploadID

	// 使用コンポーネントを受け取るため
	var $simplemovieView = null;		// 当モジュールViewコンポーネント
	var $movieView       = null;		// MovieViewコンポーネント

	/**
	 * メイン表示処理
	 *
	 * @access  public
	 */
	function execute()
	{
		
		// 変数事前定義
		$page_title    = null;
		$error_message = null;
		$output_tag    = null;
		$base_url        = BASE_URL;
		$index_file_name = INDEX_FILE_NAME;
		$css_version     = _CSS_VERSION;
		$video_tag_id    = "simplemovie_iframe_video_" . $this->movie_upload_id;
		
		// 動画UploadIDより、ブロックIDを取得する
		$block_record = $this->simplemovieView->getBlockByMovieUploadID( $this->movie_upload_id );
		if ( !empty( $block_record ) && is_array( $block_record ) && !empty( $block_record['block_id'] ) )
		{
			//***** 動画がある場合 *******************************************************
			// 再生権限があるかどうかチェックする
			list( $pathname, $filename, $physical_file_name, $cache_flag ) = 
				$this->movieView->downloadCheck( $block_record['movie_upload_id'], null, 0, "simplemovie_view_main_play", null );
			
			// キャッシュのクリア
			clearstatcache();
			
			if ( $pathname != null )
			{
				//+++++ データが存在している場合（再生権限がある場合） +++++++++++++++++++
				$autoplay     = ( intval( $block_record['autoplay_flag'] ) === 1 ) ? ' autoplay ' : '';
				$poster       = ( !empty( $block_record['thumbnail_upload_id'] ) ) ? ' poster="' . BASE_URL . INDEX_FILE_NAME . '?action=simplemovie_view_main_thumbnail&upload_id=' . $block_record['thumbnail_upload_id'] . '" ' : '';
// 2.4.2.4 ⇒ 2.4.2.5変更 start
// 元のVIDEOタグでの動画サイズは指定しないようにする。
// ※埋め込みタグを張り付けた先で表示サイズを変更したい場合があるかもしれない為
//				$video_width  = ( intval( $block_record['width'] )  !== 0 )        ? ' width="'  . $block_record['width']  . '" ' : '';
//				$video_height = ( intval( $block_record['height'] ) !== 0 )        ? ' height="' . $block_record['height'] . '" ' : '';
				$video_width  = '';
				$video_height = '';
// 2.4.2.4 ⇒ 2.4.2.5変更 end
				
				// 2.4.2.6 ⇒ 2.4.2.7 変更 start Windows7 IEの場合には、ビデオタグではなくアンカータグを出力するように変更する
				if( stristr( $_SERVER['HTTP_USER_AGENT'], "Trident" ) && stristr( $_SERVER['HTTP_USER_AGENT'], "6.1" ) && stristr( $_SERVER['HTTP_USER_AGENT'], "Windows" ) )
				{
					// VIDEOタグが使用できないブラウザの場合は、<a>タグにて対応
					$img_width  = ( intval( $block_record['width'] )  !== 0 ) ? ' width="'  . $block_record['width']  . 'px" ' : '';
					$img_height = ( intval( $block_record['height'] ) !== 0 ) ? ' height="' . $block_record['height'] . 'px" ' : '';
					
					// Windows7 ＆ IE は動画のソースをHTTP で指定する。 by nagahara@opensource-workshop.jp
					$output_tag = '動画を再生する場合は、下記画面をクリックすると、動画再生ウィンドウが開きます。<br />'
								. '※ 動画再生まで時間がかかる場合があります。<br />'
								. '<a href="' . BASE_URL_HTTP . INDEX_FILE_NAME . '?action=simplemovie_view_main_play&play_block_id=' . $block_record['block_id'] . '" target="_blank" >'
								. '<img src="' .BASE_URL . INDEX_FILE_NAME . '?action=simplemovie_view_main_thumbnail&amp;upload_id=' . $block_record['thumbnail_upload_id'] . '" style="max-width: 600px; height: auto;" ' .$img_width . $img_height .  '>'
								. '</a><br />';
				}
				else
				{
					// VIDEOタグを出力する
					$output_tag = '<video controls preload="none" style="max-width: 100%; height: auto;" id="' . $video_tag_id . '" ' . $autoplay . $poster . $video_width . $video_height . ' >'
							    . '<source src="' . BASE_URL . INDEX_FILE_NAME . '?action=simplemovie_view_main_play&play_block_id=' . $block_record['block_id'] . '" type="video/mp4" /> '
							    . '</video>';
				}
				// 2.4.2.6 ⇒ 2.4.2.7 変更 end
				
				// タイトル
				$page_title = $block_record['movie_file_name'];
			}
			else
			{
				//+++++ データが存在していない場合（再生権限がない場合） +++++++++++++++++
				$page_title = "指定された動画を閲覧できる権限がありません。";
				$error_message = $page_title;
			}
		}
		else
		{
			//***** 動画がない場合 *******************************************************
			$page_title = "指定された動画が存在していません。";
			$error_message = $page_title;
		}
		
		//***** ページ出力 ***************************************************************
print <<< EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="content-language" content="ja" />
<meta name="robots" content="noindex,nofollow" />
<link href="{$base_url}{$index_file_name}?action=common_download_css&dir_name=/simplemovie/default/style.css&vs={$css_version}" rel="stylesheet" type="text/css" />
<title>{$page_title}</title>
</head>
<body style="margin: 0px; padding: 0px;" onContextmenu="return false">
	<div class="simplemovie_iframe_area">
		{$output_tag}
		{$error_message}
	</div>
</body>
</html>
EOF;

		exit;
	}
}
?>
