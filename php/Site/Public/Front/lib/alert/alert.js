var keywa = new function() {
	this.width = $(window).width() * 0.8;
	this.height = 172;

	this.close = function() {
		$('.keywa-layer iframe').fadeOut();
		$('.keywa-layer').fadeOut();
		setTimeout(function() {
			$('.keywa-layer iframe').remove();
			$('.keywa-layer').remove();
		}, 200);
	};

	
	function messageBox(html, title, message, type){
		var jq = $(html);
		
		//判断弹窗类型设置相应样式和HTML
		if(type == "toast") {
			jq.find(".keywa-layer-panel").width("auto").css("margin-left", -message.length * 20 / 2).css("margin-top", -keywa.height / 2);
		} else {
			jq.find(".keywa-layer-panel").width(500).css({"margin-left":"-250px"});
		}
		if(valempty(title)) {
			jq.find(".keywa-layer-title").remove();
			jq.find(".keywa-layer-panel .keywa-layer-body").css("border-radius", "4px");
		} else {
			jq.find(".keywa-layer-title").find(":header").html(title);
		}
		jq.find(".keywa-layer-content").html(message.replace('\r\n', '<br/>'));
		jq.appendTo('body').fadeIn();
		var H = jq.find(".keywa-layer-panel").height();
		jq.find(".keywa-layer-panel").css({"margin-top":-H/2});
		$(".keywa-layer .keywa-btn:first").focus();
	}

	//confirm
	this.confirm = function(title, message, selected) {
		this._close = function(flag) {
			if(flag) {
				$(".keywa-layer").remove();
				selected(flag);
			} else {
				this.close();
			};
		};

		var html = '<div class="keywa-layer"><div class="keywa-layer-mask"></div><div class="keywa-layer-panel"><div class="keywa-layer-title"><h3></h3><i onclick="keywa.close()">close</i></div><div class="keywa-layer-body"><p class="keywa-layer-content"></p><p class="keywa-layer-btns"><button class="keywa-btn keywa-btn-primary" tabindex="1" onclick="keywa._close(!1)">cancel</button> <button class="keywa-btn keywa-btn-primary" onclick="keywa._close(!0)">OK</button></p></div></div></div>';
		messageBox(html, title, message);
	};

	//alert
	this.alert = function(title, message) {
		var html = '<div class="keywa-layer"><div class="keywa-layer-mask"></div><div class="keywa-layer-panel"><div class="keywa-layer-title"><h3></h3><i onclick="keywa.close()">close</i></div><div class="keywa-layer-body"><p class="keywa-layer-content"></p><p class="keywa-layer-btns"><button class="keywa-btn keywa-btn-primary" tabindex="1" onclick="keywa.close()">OK</button></p></div></div></div>';
		messageBox(html, title, message);
	}

	//toast
	this.toast = function(message, time) {
		var html = '<div class="keywa-layer"><div class="keywa-layer-toast"><p class="keywa-layer-content keywa-layer-content-toast"></p></div></div>';
		messageBox(html, "", message, "toast");
		setTimeout(function() {
			keywa.close();
		}, time || 3000);
	}
};

function valempty(str) {
	if(str == "null" || str == null || str == "" || str == "undefined" || str == undefined || str == 0) {
		return true;
	} else {
		return false;
	}
}