<?php

/**
 * 検索結果表示
 *
 * @package     jp.opensource-workshop
 * @author      nagahara@opensource-workshop.jp
 * @copyright   2014 opensource-workshop.jp
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     Opensource-workshop NetCommons2 add-on module project
 * @access      public
 */
class Simplemovie_View_Admin_Search extends Action
{
	// リクエストを受け取るため
	var $limit = null;
	var $offset = null;
	var $only_count = null;
	
	// WHERE句用パラメータ(SerachFilterによりセット)
	var $params = null;
	var $sqlwhere = null;
	
	// 値をセットするため
	var $count = 0;
	var $results = null;
	
	// Filterによりセット
	var $block_id_arr =null; 
	//var $page_id_arr =null; 
	
	// コンポーネントを受け取るため
	var $db = null;

    /**
     * 検索結果表示
     *
     * @access  public
     */
    function execute()
    {
        //// ブロックID ////
		if ($this->block_id_arr) {
			$sqlwhere = " WHERE block_id IN (".join(",", $this->block_id_arr).")";
		} else {
			return 'success';
		}
		$sqlwhere .= $this->sqlwhere;
		
		$count_res =& $this->db->execute("SELECT COUNT(*) " . 
									" FROM {simplemovie} " .
									$sqlwhere ,$this->params, null ,null, false);
		if($count_res !== false) $this->count = $count_res[0][0];
		if ($this->only_count == _ON) {
			return 'count';
		}
		if($this->count > 0) {
			$sql = "SELECT * FROM {simplemovie} " . $sqlwhere . " ORDER BY insert_time DESC";
			$this->results =& $this->db->execute($sql ,$this->params, $this->limit, $this->offset, true, array($this, '_fetchcallback'));
		}
		return 'success';
    }
    
    
	/**
	 * fetch時コールバックメソッド(blocks)
	 * @param result adodb object
	 * @access	private
	 */
	function _fetchcallback($result) {
		$ret = array();
		$count = 0;
		while ($row = $result->fetchRow()) {
			$ret[$count] = array();
			$ret[$count]['block_id'] =  $row['block_id'];
			$ret[$count]['pubDate'] = $row['insert_time'];
			//$ret[$count]['title'] = "";
			//$ret[$count]['url'] = "#";
			$ret[$count]['action'] = "simplemovie_view_main_init";
			$ret[$count]['description'] = $row['content'];
			$ret[$count]['user_id'] = $row['insert_user_id'];
			$ret[$count]['user_name'] = $row['insert_user_name'];
			$count++;
		}
		return $ret;
	}
}
?>
