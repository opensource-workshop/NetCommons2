<?php

/**
 * シンプル動画　更新系関数
 *
 * @package     jp.opensource-workshop
 * @author      nagahara@opensource-workshop.jp
 * @copyright   2014 opensource-workshop.jp
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     Opensource-workshop NetCommons2 add-on module project
 * @access      public
 */
class Simplemovie_Components_Action
{
	/**
	 * @var DBオブジェクトを保持
	 *
	 * @access	private
	 */
	var $_db = null;

	/**
	 * @var DIコンテナを保持
	 *
	 * @access	private
	 */
	var $_container = null;

	/**
	 * @var ファイルアップローダ
	 *
	 * @access	private
	 */
	var $_uploadsAction = null;
        
	/**
	 * @var compornents
	 *
	 * @access	private
	 */
	var $_simplemovieView = null;

	/**
	 * コンストラクター
	 *
	 * @access	public
	 */
	function Simplemovie_Components_Action() 
	{
		$this->_container =& DIContainerFactory::getContainer();
		$this->_db =& $this->_container->getComponent("DbObject");
		$commonMain =& $this->_container->getComponent("commonMain");
		$this->_uploadsAction =& $commonMain->registerClass(WEBAPP_DIR.'/components/uploads/Action.class.php', "Uploads_Action", "uploadsAction");
		$this->_simplemovieView =& $this->_container->getComponent("simplemovieView");
	}

	/**
	 * アップロードデータの削除
	 *
	 * @access	public
	 */
	function deleteUploads( $block_id, $movie_delete, $thumbnail_delete ) 
	{
		// 削除フラグがチェックされていなければ処理しない。
		if ( empty( $movie_delete ) && empty( $thumbnail_delete ) ) {
			return;
		}

		// テーブル確認
		$params = array( $block_id );
		$sql = "SELECT * "
			.  "FROM {simplemovie} "
			.  "WHERE block_id = ? ";
		$simplemovie_record = $this->_db->execute( $sql, $params );

		if ($simplemovie_record === false) {
			$this->_db->addError();
			return $simplemovie_record;
		}

		// データがなければ、特に処理しない。
		if ( count( $simplemovie_record ) == 0 ) {
			return;
		}

		//自動承認権限の場合は表示用カラムそれ以外は_requestカラム
		if($this->_simplemovieView->isAutoAgree($this->_simplemovieView->getAgreeFlag())) {
			$mCol = "movie_upload_id";
			$tCol = "thumbnail_upload_id";
		} else {
			$mCol = "movie_upload_id_request";
			$tCol = "thumbnail_upload_id_request";
		}
		// データがあれば、ファイルの削除
		$params = array();

		//データを承認済にする
		// ※主担以上の権限者が動画未承認時の「動画ファイルがアップロードされていません。」の表示を防ぐため
		$params["agree_flag"] = SIMPLEMOVIE_STATUS_AGREE;

		if ( $movie_delete == "1" ) {
			$this->deleteUpload( $simplemovie_record[0][$mCol] );
			$params[$mCol] = null;
		}
		if ( $thumbnail_delete == "1" ) {
			$this->deleteUpload( $simplemovie_record[0][$tCol] );
			$params[$tCol] = null;
		}

		$result = $this->_db->updateExecute(
				'simplemovie',
				$params,
				array( "block_id" => $block_id ),
				true
		);
		if ( $result === false ) {
			$this->_db->addError();
			return $result;
		}
	}

	/**
	 * アップロードされたファイルを削除する。
	 *
	 * @return boolean true or false
	 * @access public
	 */
	function deleteUpload( $upload_id )
	{
		$files = $this->_uploadsAction->delUploadsById( $upload_id );
	}

	/**
	 * uploads テーブル追加
	 *
	 * @return boolean true or false
	 * @access public
	 */
	function insUploads( $params )
	{
		return $this->_uploadsAction->insUploads( $params );
	}

	/**
	 * アップロードデータの登録
	 *
	 * @access	public
	 */
	function setUploads( $block_id, $files ) 
	{

		// アップロード・ファイルがあるかチェック、
		if ( empty( $files ) ) {
			return true;
		}

		// すでにブロックのデータがあるか確認
		$params = array( $block_id );
		$sql = "SELECT * "
			.  "FROM {simplemovie} "
			.  "WHERE block_id = ? ";
		$simplemovie_record = $this->_db->execute( $sql, $params );

		if ($simplemovie_record === false) {
			$this->_db->addError();
			return $simplemovie_record;
		}

		// SQL パラメータのクリア
		$params = array();

		//アップローダーの承認フラグをセット 1:承認済(自動承認)2:未承認
		$agreeFlg = $this->_simplemovieView->getAgreeFlag();
		$params["agree_flag"] = $agreeFlg;
		// 動画ファイルのID を処理
		if ( array_key_exists( 'movie', $files ) ) {
			$mUId = $files['movie']['upload_id'];
		}

		// サムネイル画像ファイルのID を処理
		if ( array_key_exists( 'thumbnail', $files ) ) {
			$tUId = $files['thumbnail']['upload_id'];
		}
		//自動承認権限の場合は表示用カラムそれ以外は_requestカラム
		if($this->_simplemovieView->isAutoAgree($agreeFlg) == true){
			// 管理者での更新（承認済）→xxxxx_upload_idに紐づくデータを削除
			$mCol = "movie_upload_id";
			$tCol = "thumbnail_upload_id";
		}else{
			// 主担での更新（未承認）→xxxxx_upload_id_requestに紐づくデータを削除
			$mCol = "movie_upload_id_request";
			$tCol = "thumbnail_upload_id_request";
		}
		if ($mUId) $params[$mCol] = $mUId;
		if ($tUId) $params[$tCol] = $tUId;

		// データがあれば更新
		if ( count( $simplemovie_record ) > 0 ) {
			$rowMUId = $simplemovie_record[0][$mCol];
			$rowTUId = $simplemovie_record[0][$tCol];
			// 既存のファイルがあり、新たにアップロードされている場合、既存のファイルを削除
			if ( !empty( $rowMUId ) && array_key_exists( 'movie', $files ) && !empty( $files['movie']['upload_id'] ) ) {
				$this->deleteUpload( $rowMUId );
			}
			if ( !empty( $rowTUId ) && array_key_exists( 'thumbnail', $files ) && !empty( $files['thumbnail']['upload_id'] ) ) {
				$this->deleteUpload( $rowTUId );
			}

			$result = $this->_db->updateExecute(
				'simplemovie',
				$params,
				array( "block_id"=> $block_id ),
				true
			);
		}
		// データがなければ追加
		else {

			$params["block_id"] = $block_id;
			$result = $this->_db->insertExecute(
				'simplemovie',
				$params,
				true
			);
		}
        }

	/**
	 *  表示方法の登録
	 * @param func_parmas array( block_id, default_width, default_height, autoplay_flag, embed_show_flag )
	 * @access	public
	 */
	function setStyle( $func_parmas ) 
	{

		// 引数が配列じゃなければ異常終了
		if ( !is_array( $func_parmas ) ) return false;
		
		// ブロックIDの取得
		$block_id = array_key_exists( 'block_id', $func_parmas ) ? $func_parmas['block_id'] : null;

		// すでにブロックのデータがあるか確認
		$params = array( $block_id );
		$sql = "SELECT * "
			.  "FROM {simplemovie} "
			.  "WHERE block_id = ? ";
		$simplemovie_record = $this->_db->execute( $sql, $params );

		if ($simplemovie_record === false) {
			$this->_db->addError();
			return $simplemovie_record;
		}

		// SQL パラメータのクリア
		$params = array();

		if ( array_key_exists( 'default_width'  , $func_parmas ) ) $params["width"]           = $func_parmas['default_width'];
		if ( array_key_exists( 'default_height' , $func_parmas ) ) $params["height"]          = $func_parmas['default_height'];
		if ( array_key_exists( 'autoplay_flag'  , $func_parmas ) ) $params["autoplay_flag"]   = $func_parmas['autoplay_flag'];
		if ( array_key_exists( 'embed_show_flag', $func_parmas ) ) $params["embed_show_flag"] = $func_parmas['embed_show_flag'];
		
		// Updateする項目がない場合には何もせず終了
		if ( count( $params ) <= 0 ) return false;
		
		// データがあれば更新
		if ( count( $simplemovie_record ) > 0 ) {

			$result = $this->_db->updateExecute(
				'simplemovie',
				$params,
				array( "block_id"=> $block_id ),
				true
			);
		}
		// データがなければ追加
		else {

			$params["block_id"] = $block_id;
			$result = $this->_db->insertExecute(
				'simplemovie',
				$params,
				true
			);
		}
	}

}
?>
