//	Copyright (C) Http://www.phpstcms.com/
//	Author: me@yangdahong.cn
//	All rights reserved

$.fn.extend({
	wxplayer:function(container, loop) {
		var eles = $(this);
		var playlist = new Array();
		var thisobj = null;
		var thisindex = 0;
		var autoloop = loop; 
		if($("#_wxplayer").length < 1) {
			$('<audio src="/images/sample.mp3" id="_wxplayer" style="width:0; height:0;" preload="auto"></audio>').appendTo($("body"));
		} else {
			var thisitem = $(eles).filter(".playing");
			thisobj = {"id":$(thisitem).data("id"), "url":$(thisitem).data("url")};
			thisindex = $(thisitem).get();
		}
		var handle = $("#_wxplayer").get(0);
		$(handle).bind("ended", function() {
			$(eles).removeClass("playing");
			if(autoloop) {
				next();
			}
		});
		$(handle).bind("pause", function() {
			$(eles).removeClass("playing");
		});
		$(handle).bind("play", function() {
			$(eles).removeClass("playing");
			$(eles).filter("[data-id="+thisobj['id']+"]").addClass("playing").data("played", "1").find("i").hide();
		});
		$(eles).each(function(index, element) {
			var duration = parseInt($(this).find("em").html());
			if(duration >= 300) {
				width = 100;
			} else if(duration >= 60) {
				width = 70 + (duration - 60)*30/240;
			} else {
				width = duration*70/60;
			}
			var $parentwidth = container ?  $(container).innerWidth() : 300;
			$(this).css({"width":($parentwidth - 135)*width/100+"px"});
			if(/\.mp3$/i.test($(this).data("url"))) {
				playlist.push({"id":$(this).data("id"), "url":$(this).data("url")});
				$(this).click(function() {
					if(handle.paused) {
						play($(this).data("id"));
					} else {
						if($(this).data("id") != thisobj['id']) {
							play($(this).data("id"));
						} else {
							handle.pause();
						}
					}
				});
			}
		});
		function next() {
			if(playlist == null) return;
			if(thisobj == null) {
				thisindex = 0;
			} else {
				if(thisindex + 1 < playlist.length && $(eles).filter("[data-id="+playlist[thisindex + 1]['id']+"]").data("played") == "0") {
					thisindex = thisindex+1;
				} else {
					return;
				}
			}
			thisobj = playlist[thisindex];
			$(handle).attr("src", thisobj['url']);
			handle.play();
		}
		function play(id) {
			if(playlist == null) return;
			for(var n=0; n<playlist.length; n++) {
				if(playlist[n]['id'] == id) {
					thisindex = n;
					thisobj = playlist[n];
					$(handle).attr("src", thisobj['url']);
					handle.play();
				}
			}
		}
	}
});