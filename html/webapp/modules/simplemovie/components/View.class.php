<?php

/**
 * シンプル動画　取得系関数
 *
 * @package     jp.opensource-workshop
 * @author      nagahara@opensource-workshop.jp
 * @copyright   2014 opensource-workshop.jp
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     Opensource-workshop NetCommons2 add-on module project
 * @access      public
 */
class Simplemovie_Components_View
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
	
// ★ 2.4.2.5 ⇒ 2.4.2.6 追加 start
	/**
	 * @var Sessionオブジェクトを保持
	 *
	 * @access	private
	 */
	 var $_session = null;
// ★ 2.4.2.5 ⇒ 2.4.2.6 追加 end

	/**
	 * コンストラクター
	 *
	 * @access	public
	 */
	function Simplemovie_Components_View()
	{
		$this->_container =& DIContainerFactory::getContainer();
		$this->_db        =& $this->_container->getComponent("DbObject");
		$this->_session   = $this->_container->getComponent("Session");		// ★ 2.4.2.5 ⇒ 2.4.2.6 追加
	}

	/**
	 * ブロックデータの取得
	 *
	 * @access	public
	 */
	function getBlock( $block_id )
	{
		// テーブル確認
		$params = array( $block_id );
		$sql = "SELECT "
			.  "s.*, "
			.  "m.file_name AS movie_file_name, "
			.  "t.file_name AS thumbnail_file_name, "
			.  "mr.file_name AS movie_file_name_request, "
			.  "tr.file_name AS thumbnail_file_name_request "
			.  "FROM {simplemovie} s "
			.    "LEFT JOIN {uploads} m ON m.upload_id = s.movie_upload_id "
			.    "LEFT JOIN {uploads} t ON t.upload_id = s.thumbnail_upload_id "
			.    "LEFT JOIN {uploads} mr ON mr.upload_id = s.movie_upload_id_request "
			.    "LEFT JOIN {uploads} tr ON tr.upload_id = s.thumbnail_upload_id_request "
			.  "WHERE s.block_id = ? ";
		$simplemovie_record = $this->_db->execute( $sql, $params );

		if ($simplemovie_record === false) {
			$this->_db->addError();
			return $simplemovie_record;
		}

		if ( !empty( $simplemovie_record ) && count( $simplemovie_record ) > 0 ) {
			return $simplemovie_record[0];
		}
		return $simplemovie_record;
	}

	/**
	 * 動画UploadIDからブロックデータを取得
	 *
	 * @access	public
	 */
	public function getBlockByMovieUploadID( $movie_upload_id )
	{
		// 条件設定
		$params = array( $movie_upload_id );
		
		// SQL文作成（uploadsテーブルにないとダメなので、inner joinを行っている）
		$sql = "SELECT s.*, u.file_name AS movie_file_name "
			.  "FROM {simplemovie} s "
			.  "INNER JOIN {uploads} u ON u.upload_id = s.movie_upload_id "
			.  "WHERE s.movie_upload_id = ? ";
		
		// SQL実施
		$simplemovie_record = $this->_db->execute( $sql, $params );
		
		// 結果
		if ( $simplemovie_record === false )
		{
			$this->_db->addError();
			return false;
		}
		else if( empty( $simplemovie_record ) || count( $simplemovie_record) <= 0 )
		{
			return false;
		}
		
		return $simplemovie_record[0];
	}

// ★ 2.4.2.5 ⇒ 2.4.2.6 追加 start
	/**
	 * ログインユーザーの権限を取得
	 *
	 * @return	int		-1:ログインしてない / 0以上:ログインユーザーの基本権限値
	 * @access	public
	 */
	public function getLoginAuth()
	{
		// ユーザーID取得
		$user_id = $this->_session->getParameter("_user_id");
		if( $user_id == "0" )
		{
			// "0"の場合はログインしていない
			return -1;
		}
		
		// ログインユーザー権限を取得して返却
		return $this->_session->getParameter("_user_auth_id");
		
	}
// ★ 2.4.2.5 ⇒ 2.4.2.6 追加 end
	/**
	 * 自身の権限の承認フラグを返却
	 * @return	int		1:承認済 2:未承認
	 * @access	public
	 */
	function getAgreeFlag() 
	{
		// ユーザ権限を取得
		if ( $this->_session->getParameter("_user_auth_id") <= _AUTH_CHIEF ) {
			// 主担以下の権限の場合、承認待ちにする
			$agree_flag = SIMPLEMOVIE_STATUS_RESERVE;
		} else {
			// 主担より上位(管理者)の場合、承認済みにする
			$agree_flag = SIMPLEMOVIE_STATUS_AGREE;
		}
		return $agree_flag;
	}

	/**
	 *  自動承認の可否状態を返却（管理者の場合にtrueを返却を想定）
	 * @access	public
	 */
	function isAutoAgree($agree_flag) 
	{
		if ( $agree_flag == SIMPLEMOVIE_STATUS_AGREE) {
			return true;
		}
		return false;
	}
}
?>
