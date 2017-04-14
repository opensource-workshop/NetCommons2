<?php

/**
 * [[機能説明]]
 *
 * @package     jp.opensource-workshop
 * @author      nagahara@opensource-workshop.jp
 * @copyright   2014 opensource-workshop.jp
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     Opensource-workshop NetCommons2 add-on module project
 * @access      public
 */
class Simplemovie_Action_Main_Init extends Action
{
    // リクエストパラメータを受け取るため
    var $block_id = null;
    var $page_id = null;
    var $room_id = null;

    // 使用コンポーネントを受け取るため
    var $simplemovieView = null;
    var $simplemovieAction = null;

    // 値をセットするため

    /**
     * [[機能説明]]
     *
     * @access  public
     */
    function execute()
    {
        return 'success';
    }
}
?>
