<?php

/**
 * 動画アップロード・バリデータ
 *
 * @package     jp.opensource-workshop
 * @author      nagahara@opensource-workshop.jp
 * @copyright   2014 opensource-workshop.jp
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     Opensource-workshop NetCommons2 add-on module project
 * @access      public
 */
class Simplemovie_Validator_Movieupload extends Validator
{

	/**
	 * @var uploadsActionオブジェクトを保持
	 *
	 * @access	private
	 */
	var $_uploadsAction = null;

	// MovieActionコンポーネント
	private $_movieAction = null;

	// simplemovieViewコンポーネント
	private $_simplemovieView = null;

        /**
	 * 動画アップロード・バリデータ
	 *
	 * @param   mixed   $attributes チェックする値
	 * @param   string  $errStr     エラー文字列
	 * @param   array   $params     オプション引数
	 * @return  string  エラー文字列(エラーの場合)
	 * @access  public
	 */
	function validate($attributes, $errStr, $params)
	{

		// NetCommons コンポーネント
		$container =& DIContainerFactory::getContainer();

		// リクエスト・コンポーネント
		$request =& $container->getComponent("Request");

		// データ取得コンポーネント
		$this->_simplemovieView =& $container->getComponent("simplemovieView");

		// 動画の削除チェック
		$movie_delete = $request->getParameter("movie_delete");

		// サムネイルの削除チェック
		$thumbnail_delete = $request->getParameter("thumbnail_delete");
		
		// 動画変換実施フラグ
		$change_execute_flag = $request->getParameter("change_execute_flag");

		$movie_required_flag     = SIMPLEMOVIE_REQUIRE_MOVIE;			// 動画必須フラグ取得
		$thumbnail_required_flag = SIMPLEMOVIE_REQUIRE_THUMBNAIL;		// サムネイル必須フラグ取得

		// ファイルアップロード
		$fileUpload =& $container->getComponent("FileUpload");
		$commonMain =& $container->getComponent("commonMain");
		$this->_uploadsAction =& $commonMain->registerClass(WEBAPP_DIR.'/components/uploads/Action.class.php', "Uploads_Action", "uploadsAction");
		$files = $this->_uploadsAction->uploads();
		$errormes = $fileUpload->getErrorMes();
		//test_log($files);
		//test_log($errormes);

		// MovieActionコンポーネント取得
		$this->_movieAction = $commonMain->registerClass(WEBAPP_DIR.'/components/movie/Action.class.php', "Movie_Action", "movieAction");

		// 現在のブロックデータを取得
		$simplemovie = $this->_simplemovieView->getBlock( $request->getParameter( "block_id" ) );

		// 必須チェック

		// 動画が必須の場合は必須チェックを行う
		if ( SIMPLEMOVIE_REQUIRE_MOVIE == true )
		{
			if ( $this->existBlockMovie( $simplemovie ) === false && $this->checkUploadMovie( $files ) === false )
			{
				$this->deleteUpload( $files );
				return "動画ファイルは必須です。指定してください。";
			}
		}
                // サムネイルの場合、必須であり、かつ、動画変換を行わない場合に必須チェックを行う
		if ( SIMPLEMOVIE_REQUIRE_THUMBNAIL == true &&
		     ( empty( $change_execute_flag ) || intval( $change_execute_flag ) !== 1 ) )
		{
			if ( $this->existBlockThumbnail( $simplemovie ) === false && $this->checkUploadThumbnail( $files ) === false )
			{
				$this->deleteUpload( $files );
				return "サムネイル画像ファイルは必須です。指定してください。";
			}
		}

                // ファイルの添付がない場合もあるので、エラーメッセージが「ファイルを指定してください」の場合は、エラーにしない。
		$nofile_error_key = array_search( _FILE_UPLOAD_ERR_UPLOAD_NOFILE, $errormes );
		if ( $nofile_error_key !== false ) {
			unset( $errormes[$nofile_error_key] );
		}

		// NetCommons アップロード部品が返すエラー処理(設定したファイルサイズのオーバー)
		if ( !empty( $errormes ) && is_array( $errormes ) && count( $errormes ) > 0 ) {
			$error_str = "";
			foreach ( $errormes as $errorme ) {
				$error_str = $error_str . $errorme . "<br />";
			}
			return $error_str;
		}

		// 拡張子チェック（サムネイル）
		$error_message = $this->checkExtensionThumbnail( $files );
		if ( !empty( $error_message ) ) {
			$this->deleteUpload( $files );
			return $error_message;
		}
		
		// サムネイルを自動作成するかどうかのフラグを設定
		$thumbnail_create_flag = false;
		if ( $this->checkUploadMovie( $files ) === true &&
			 $this->checkUploadThumbnail( $files ) === false &&
			 ( $this->existBlockThumbnail( $simplemovie ) === true && !empty( $thumbnail_delete ) ||
			   $this->existBlockThumbnail( $simplemovie ) === false ) )
		{
			// 動画が新規Uploadされていて、かつ
			// サムネイルが新規Uploadされておらず、かつ
			// ブロックにサムネイルが登録されているが削除指定がされている、または、ブロックにサムネイルがまだ登録されていない場合
			$thumbnail_create_flag = true;
		}

		// 変換アリの場合、動画変換処理を実施する
		if ( !empty( $change_execute_flag ) && intval( $change_execute_flag ) === 1 ) {
			// 動画変換処理実施
			$error_message = $this->_movieAction->convertMovie( $files, "simplemovie", $thumbnail_create_flag );
			if ( !empty( $error_message ) )
			{
				$this->deleteUpload( $files );
				return $error_message;
			}
		}
		else
		{
			// 動画の拡張子チェック
			$error_message = $this->checkExtensionMovie( $files );
			if ( !empty( $error_message ) ) {
				$this->deleteUpload( $files );
				return $error_message;
			}
		}

		// リクエスト内容に値を書いて、Actionクラスに受け渡す
		$request->setParameter( "upload_files", $files );

		return;
	}


	/**
	 * アップロードされたファイルの拡張子チェック（動画）
	 * 　※動画変換しない場合のチェック
	 *
	 * @return boolean true or false
	 * @access public
	 */
	function checkExtensionMovie( $files )
	{
		// ファイルがアップロードされていない場合は拡張子チェックOK
		if ( $this->checkUploadMovie( $files ) === false ) return;

		// 動画
		$allow_movie_extension = explode( ',', MOVIE_ALLOW_MOVIE_EXTENSION_NOCHANGE );
		if ( array_key_exists( 'movie', $files ) ) {
			// 拡張子を小文字に変換
			$extension = mb_strtolower( $files['movie']['extension'] );
			if ( !in_array( $extension, $allow_movie_extension ) ) {
				return "エラー！<br />動画として許可された拡張子以外のファイルがアップロードされました。";
			}
		}

		return;
	}

	/**
	 * アップロードされたファイルの拡張子チェック（サムネイル）
	 *
	 * @return boolean true or false
	 * @access public
	 */
	function checkExtensionThumbnail( $files )
	{
		// ファイルがアップロードされていない場合は拡張子チェックOK
		if ( $this->checkUploadThumbnail( $files ) === false ) return;
		
		// サムネイル画像
		$allow_thumbnail_extension = explode( ',', SIMPLEMOVIE_ALLOW_THUMBNAIL_EXTENSION );
		if ( array_key_exists( 'thumbnail', $files ) ) {
			// 拡張子を小文字に変換
			$extension = mb_strtolower( $files['thumbnail']['extension'] );
			if ( !in_array( $extension, $allow_thumbnail_extension ) ) {
				return "エラー！<br />サムネイル画像として許可された拡張子以外のファイルがアップロードされました。";
			}
		}

		return;
	}

	/**
	 * アップロードされたファイルを削除する。
	 *
	 * @return boolean true or false
	 * @access public
	 */
	function deleteUpload( $files )
	{
		foreach ( $files as $file ) {
			$files = $this->_uploadsAction->delUploadsById( $file['upload_id'] );
		}
	}
	
	
	/**
	 * 動画が新規Uploadされたかどうかチェック
	 *
	 * @return boolean true:newUpload or false:noUpload
	 * @access private
	 */
	private function checkUploadMovie( $files )
	{
		// $filesの中に['movie']がない、または、'movie'のupload_idがない ＝ 新規登録されていない
		if ( !array_key_exists( 'movie', $files ) || empty( $files['movie']['upload_id'] ) ) return false;
		
		return true;
	}
	/**
	 * サムネイルが新規Uploadされたかどうかチェック
	 *
	 * @return boolean true:newUpload or false:noUpload
	 * @access private
	 */
	private function checkUploadThumbnail( $files )
	{
		// $filesの中に['thumbnail']がない、または、'thumbnail'のupload_idがない ＝ 新規登録されていない
		if ( !array_key_exists( 'thumbnail', $files ) || empty( $files['thumbnail']['upload_id'] ) ) return false;
		
		return true;
	}
	/**
	 * 動画が既にブロックに登録されているかどうかチェック
	 *
	 * @return boolean true:exist or false:noExist
	 * @access private
	 */
	private function existBlockMovie( $block_data )
	{
		// ブロック情報が空 or ブロック情報が0件 or ブロック情報に動画のUploadIDがない ＝ ブロックに動画が登録されていない
		if ( empty( $block_data ) || count( $block_data ) <= 0 || empty( $block_data['movie_upload_id'] ) ) return false;
		
		return true;
	}
	/**
	 * サムネイルが既にブロックに登録されているかどうかチェック
	 *
	 * @return boolean true:exist or false:noExist
	 * @access private
	 */
	private function existBlockThumbnail( $block_data )
	{
		// ブロック情報が空 or ブロック情報が0件 or ブロック情報にサムネイルのUploadIDがない ＝ ブロックにサムネイルが登録されていない
		if ( empty( $block_data ) || count( $block_data ) <= 0 || empty( $block_data['thumbnail_upload_id'] ) ) return false;

		//コンテンツの承認が必要な会員による更新の場合にはサムネイルは必須
		if($this->_simplemovieView->isAutoAgree($this->_simplemovieView->getAgreeFlag()) == false)
		{
			if(empty( $block_data['thumbnail_upload_id_request'] )) return false;
		}
		
		return true;
	}

}
?>