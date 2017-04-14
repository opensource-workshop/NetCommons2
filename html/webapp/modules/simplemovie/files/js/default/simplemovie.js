var clsSimplemovie = Class.create();
var simplemovieCls = Array();

clsSimplemovie.prototype = {
	initialize: function(id) {
		this.id = id;
	},

	/* 動画、サムネイルをアップロードする */
	uploadEditForm: function(eventObject, form_el) {

		// dialog
		if ( form_el["attachment[movie]"].value || form_el["attachment[thumbnail]"].value ) {

			var queryString = "action=simplemovie_view_main_uploading"
									+ "&prefix_id_name=popup_uploading";

			var option = new Object();
			option["top_el"] = $(this.id);
			option["modal_flag"] = true;
			option["center_flag"] = true;
			commonCls.sendPopupView(eventObject, queryString, option);
		}

		var messageBody = new Object();
		messageBody["action"] = "simplemovie_action_edit_init";

		// POSTオプションの設定
		var option = new Object();
		option["param"] = messageBody;
		option["top_el"] = $(this.id);
		option["timeout_flag"] = false;
		option["callbackfunc"] = function(res) {
			commonCls.alert('更新しました。');
			if ( form_el["attachment[movie]"].value || form_el["attachment[thumbnail]"].value ) {
				commonCls.removeBlock("_popup_uploading"+this.id);
			}
			commonCls.sendRefresh(this.id);
		}.bind(this);
		option["callbackfunc_error"] = function(file, res){
			commonCls.alert(res);
			if ( form_el["attachment[movie]"].value || form_el["attachment[thumbnail]"].value ) {
				commonCls.removeBlock("_popup_uploading"+this.id);
			}
		}.bind(this);
		commonCls.sendAttachment(option);
	},

	// 表示設定
	setStyle: function() {

		var fromElement = $("simplemovieForm" + this.id);
		var messageBody = Form.serialize(fromElement);

		var option = new Object();
		option["target_el"] = $(this.id);
		option["callbackfunc"] = function(){
			alert("更新しました。");
			//commonCls.sendRefresh(this.id);
		}.bind(this);

		commonCls.sendPost(this.id, messageBody, option);
	},
	
	// 動画ダウンロード
	movieDownload: function( block_id, movie_upload_id )
	{
		var set_id = block_id.replace("_", "");
		
		// 呼び出し
		var href = _nc_base_url + _nc_index_file_name + '?block_id=' + set_id + '&action=simplemovie_view_edit_download&upload_id=' + movie_upload_id;
		location.href = href;
	},
	
	// 埋め込みタグコード（iframeタグコード）生成
	createIFrameValue: function( embed_code )
	{
		// VIDEOタグエレメント取得
		var videoEl = $("simplemovie_video" + this.id);
		if( videoEl == null )
		{
			// サムネイルエレメント取得して、そこからサイズを取得する
			var thumbnailEl = $("simplemovie_thumbnail" + this.id);
			var setWidth  = thumbnailEl.getWidth()  +20;	// 少し大きめに指定する。
			var setHeight = thumbnailEl.getHeight() +60;	// 注意文言が入る事を想定して、少し大きく指定する。
		}
		else
		{
			// VIDEOエレメントからサイズを取得する
			var setWidth  = videoEl.getWidth();
			var setHeight = videoEl.getHeight() ;
		}
		
		var result = "<iframe style=\"width:" + setWidth + "px; height:" + setHeight + "px;\" " + embed_code + "></iframe>";
		
		$("simplemovie_embed_code" + this.id).value = result;
	},

	/* 承認 */
	agreeSimpleClick: function(agree_flag) {
		// 確認メッセージ
		var confirmMessage = "決定するとこの動画が承認されます。\nよろしいですか？";
		var callbackmessage = "承認しました。";
		if (!commonCls.confirm(confirmMessage)) {
			return false;
		}
		var option = new Object();
		option["target_el"] = $(this.id);
		option["callbackfunc"] = function(response) {
								commonCls.alert(callbackmessage);
		}.bind(this);
		commonCls.sendPost(this.id, "action=simplemovie_action_main_agree&agree_flag=" + agree_flag, option);
	},
}