<?php

/**
 * モジュール操作時(move,copy,shortcut)に呼ばれるアクション
 *
 * @package     jp.opensource-workshop
 * @author      nagahara@opensource-workshop.jp
 * @copyright   2014 opensource-workshop.jp
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     Opensource-workshop NetCommons2 add-on module project
 * @access      public
 */
class Simplemovie_Action_Admin_Operation extends Action
{
	var $mode = null;	//move or shortcut or copy

	// 移動元
	var $block_id = null;
	//var $page_id = null;
	//var $module_id = null;
	//var $unique_id = null;

	// 移動先
	var $move_page_id = null;
	var $move_room_id = null;
	var $move_block_id = null;

	// コンポーネントを受け取るため
	var $db = null;
	var $simplemovieView = null;
	var $simplemovieAction = null;
	//var $commonOperation = null;
	//var $whatsnewAction = null;
	var $session = null;
	var $uploadsAction = null;

	/**
	 * モジュールコピー or 移動
	 *
	 * @access  public
	 */
	function execute()
	{

		// 更新前の値を取得しておく
		$bofore_simplemovie = $this->simplemovieView->getBlock( $this->block_id );

		switch ($this->mode) {
			case "move":

				// 動画レコード更新
				$params = array(
					"block_id"=> intval($this->move_block_id),
					"room_id"=> intval($this->move_room_id)
				);
				$where_params = array(
					"block_id"=> intval($this->block_id)
				);
				$result = $this->db->updateExecute("simplemovie", $params, $where_params, false);
				if($result === false) {
					return "false";
				}

				// 動画ファイル更新処理(room_id を更新する)
				if ( $bofore_simplemovie['room_id'] != $this->move_room_id ) {

					// 動画ファイルがある場合
					if ( !empty( $bofore_simplemovie['movie_upload_id'] ) ) {

						// uploads テーブル更新
						$movie_params = array( "room_id"=> intval($this->move_room_id) );
						$movie_where = array( "upload_id"=> intval($bofore_simplemovie['movie_upload_id']) );
						$movie_result = $this->db->updateExecute("uploads", $movie_params, $movie_where, false);
						if($movie_result === false) {
							return "false";
						}
					}

					// サムネイル画像ファイルがある場合
					if ( !empty( $bofore_simplemovie['thumbnail_upload_id'] ) ) {

						// uploads テーブル更新
						$thumbnail_params = array( "room_id"=> intval($this->move_room_id) );
						$thumbnail_where = array( "upload_id"=> intval($bofore_simplemovie['thumbnail_upload_id']) );
						$thumbnail_result = $this->db->updateExecute("uploads", $thumbnail_params, $thumbnail_where, false);
						if($thumbnail_result === false) {
							return "false";
						}
					}

					// リクエスト動画ファイルがある場合
					if ( !empty( $bofore_simplemovie['movie_upload_id_request'] ) ) {

						// uploads テーブル更新
						$movie_params = array( "room_id"=> intval($this->move_room_id) );
						$movie_where = array( "upload_id"=> intval($bofore_simplemovie['movie_upload_id_request']) );
						$movie_result = $this->db->updateExecute("uploads", $movie_params, $movie_where, false);
						if($movie_result === false) {
							return "false";
						}
					}

					// リクエストサムネイル画像ファイルがある場合
					if ( !empty( $bofore_simplemovie['thumbnail_upload_id_request'] ) ) {

						// uploads テーブル更新
						$thumbnail_params = array( "room_id"=> intval($this->move_room_id) );
						$thumbnail_where = array( "upload_id"=> intval($bofore_simplemovie['thumbnail_upload_id_request']) );
						$thumbnail_result = $this->db->updateExecute("uploads", $thumbnail_params, $thumbnail_where, false);
						if($thumbnail_result === false) {
							return "false";
						}
					}

				}

				//--新着情報関連 Start--
				//$whatsnew = array(
				//	"unique_id" => $this->block_id,
				//	"room_id" => $this->move_room_id
				//);
				//$result = $this->whatsnewAction->moveUpdate($whatsnew);
				//if ($result === false) {
				//	return false;
				//}
				//--新着情報関連 End--

				break;

			case "copy":

				// 添付ファイルコピー処理

				// uploads テーブル取得
				$upload_id_arr = array();

				// コピーした動画ファイルの upload_id
				$movie_upload_id = null;

				// コピーしたサムネイル画像ファイルの upload_id
				$thumbnail_upload_id = null;

				// コピーしたリクエスト動画ファイルの upload_id
				$movie_upload_id_request = null;

				// コピーしたリクエストサムネイル画像ファイルの upload_id
				$thumbnail_upload_id_request = null;

				// 動画ファイルがある場合
				if ( !empty( $bofore_simplemovie['movie_upload_id'] ) ) {
					$movie_upload_id = $this->copyUploads( $bofore_simplemovie['movie_upload_id'], $this->move_room_id );
				}

				// サムネイル画像ファイルがある場合
				if ( !empty( $bofore_simplemovie['thumbnail_upload_id'] ) ) {
					$thumbnail_upload_id = $this->copyUploads( $bofore_simplemovie['thumbnail_upload_id'], $this->move_room_id );
				}

				// リクエスト動画ファイルがある場合
				if ( !empty( $bofore_simplemovie['movie_upload_id_request'] ) ) {
					$movie_upload_id_request = $this->copyUploads( $bofore_simplemovie['movie_upload_id_request'], $this->move_room_id );
				}

				// リクエストサムネイル画像ファイルがある場合
				if ( !empty( $bofore_simplemovie['thumbnail_upload_id_request'] ) ) {
					$thumbnail_upload_id_request = $this->copyUploads( $bofore_simplemovie['thumbnail_upload_id_request'], $this->move_room_id );
				}

				// 動画テーブルの追加
				$params = array(
				    'block_id'                    => $this->move_block_id,
				    'movie_upload_id'             => $movie_upload_id,
				    'thumbnail_upload_id'         => $thumbnail_upload_id,
				    'movie_upload_id_request'     => $movie_upload_id_request,
				    'thumbnail_upload_id_request' => $thumbnail_upload_id_request,
				    'width'                       => $bofore_simplemovie['width'],
				    'height'                      => $bofore_simplemovie['height'],
				    'room_id'                     => $this->move_room_id,
					//コピーした場合にも
				    'agree_flag'                  => $bofore_simplemovie['agree_flag'],
				);
				$result = $this->db->insertExecute(
					'simplemovie',
					$params,
					true
				);
				break;
			default:
				return "false";
		}

		return "true";
	}

	/**
	 * 添付ファイルのコピー
	 *
	 * @access  public
	 */
	function copyUploads( $upload_id, $move_room_id )
	{

		// upload_id から uploads テーブルの取得
		$where_params = array( "upload_id" => $upload_id );

		$uploads = $this->db->selectExecute( "uploads", $where_params );
		if( $uploads === false ) {
			return false;
		}
		if( !isset( $uploads[0] ) ) {
			return array();
		}

		// uploads テーブルの追加
		$params = array(
		    'room_id'             => $move_room_id,
		    'module_id'           => $uploads[0]['module_id'],
		    'unique_id'           => $uploads[0]['unique_id'],
		    'file_name'           => $uploads[0]['file_name'],
		    'physical_file_name'  => "",
		    'file_path'           => $uploads[0]['file_path'],
		    'action_name'         => $uploads[0]['action_name'],
		    'file_size'           => $uploads[0]['file_size'],
		    'mimetype'            => $uploads[0]['mimetype'],
		    'extension'           => $uploads[0]['extension'],
		    'garbage_flag'        => $uploads[0]['garbage_flag']
		);
		$upload_id = $this->simplemovieAction->insUploads( $params );
		if ( $upload_id === false ) {
			return false;
		}

		//サムネイル自動作成時には動画のupload_id+_thumbnailで作成される ex.600_thumbnail.jpg サムネイル画像自体のupload_idは601
		//コピー元が自動作成サムネイルの場合に処理を実行
		$autoCreateThumbFilename = $uploads[0]['file_name'];
		if(preg_match('/^[0-9]*_thumbnail.*/', $autoCreateThumbFilename)){
			copy( FILEUPLOADS_DIR.$uploads[0]['file_path'].$autoCreateThumbFilename,
				  FILEUPLOADS_DIR.$uploads[0]['file_path'].$upload_id.".".$uploads[0]['extension'] );
			//コピーしたのでリターン
			return $upload_id;
		}

		// ファイルのコピー(サムネイルがある場合はサムネイルも)
		$file_full_path = FILEUPLOADS_DIR.$uploads[0]['file_path'].$uploads[0]['upload_id']."*.*";
		foreach ( glob( $file_full_path ) as $filename ) {

			$filename = basename( $filename );

			// メインのファイル
			if ( $filename == $uploads[0]['physical_file_name'] ) {
				copy( FILEUPLOADS_DIR.$uploads[0]['file_path'].$filename,
				      FILEUPLOADS_DIR.$uploads[0]['file_path'].$upload_id.".".$uploads[0]['extension'] );
			}
			// サムネイル(240)
			elseif ( $filename == $uploads[0]['upload_id'] . "_mobile_" . MOBILE_IMGDSP_SIZE_240 .".". $uploads[0]['extension'] ) {
				copy( FILEUPLOADS_DIR.$uploads[0]['file_path'].$uploads[0]['upload_id'] . "_mobile_" . MOBILE_IMGDSP_SIZE_240 .".". $uploads[0]['extension'],
				      FILEUPLOADS_DIR.$uploads[0]['file_path'].$upload_id               . "_mobile_" . MOBILE_IMGDSP_SIZE_240 .".". $uploads[0]['extension'] );
			}
			// サムネイル(480)
			elseif ( $filename == $uploads[0]['upload_id'] . "_mobile_" . MOBILE_IMGDSP_SIZE_480 .".". $uploads[0]['extension'] ) {
				copy( FILEUPLOADS_DIR.$uploads[0]['file_path'].$uploads[0]['upload_id'] . "_mobile_" . MOBILE_IMGDSP_SIZE_480 .".". $uploads[0]['extension'],
				      FILEUPLOADS_DIR.$uploads[0]['file_path'].$upload_id               . "_mobile_" . MOBILE_IMGDSP_SIZE_480 .".". $uploads[0]['extension'] );
			}
		}
		
		// 新しいupload_id の返却
		return $upload_id;
	}
}
?>