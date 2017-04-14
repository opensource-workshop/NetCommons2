<?php

/**
 * シンプル動画承認表示
 *
 * @package	 NetCommons
 * @author	  Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license	 http://www.netcommons.org/license.txt  NetCommons License
 * @project	 NetCommons Project, supported by National Institute of Informatics
 * @access	  public
 */
class Simplemovie_View_Main_Agree extends Action
{
	// リクエストパラメータを受け取るため
	var $block_id = null;

	// 使用コンポーネントを受け取るため
	var $db = null;
	var $mobileView = null; // mod by AllCreator
	var $session = null;	// add by AllCreator

	// 値をセットするため

	function execute()
	{

		return 'success';
	}
}
?>