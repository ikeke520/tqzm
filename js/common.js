function check_all(id, name) {
	if(!name) {
		name = 'id';
	}
	$("form#"+id+" input:checkbox[name='"+name+"']").each(function(index, element) {
		$(this).prop("checked", true);
	});
}
function anti_all(id, name) {
	if(!name) {
		name = 'id';
	}
	$("form#"+id+" input:checkbox[name='"+name+"']").each(function(index, element) {
		$(this).prop("checked", $(this).prop('checked') ? false : true);
	});
}
function get_all_value(id, name, splitvar) {
	var id_array = Array();
	if(!name) {
		name = 'id';
	}
	if(!splitvar) {
		splitvar = ',';
	}
	$("form#"+id+" input:checkbox[name='"+name+"']").each(function(index, element) {
		if($(this).prop('checked')) {
			id_array.push($(this).val());
		}
	});
	return id_array.join(splitvar);
}
function in_array(a,b) {
	for(i in b){
		if(b[i]== a) return true;
	}
	return false;
}
function init_checkbox(name, values) {
	$("input:checkbox[name='"+name+"']").each(function(i){
		if(in_array($(this).val(), values)) {
			$(this).prop('checked', true);
		}
	});
}
function init_radio(name, value) {
	$("input:radio[name='"+name+"']").each(function(i){
		if($(this).val() == value) {
			$(this).prop('checked', true);
		}
	});
}
function get_radio_value(name) {
	var value=false;
	$("input:radio[name='"+name+"']").each(function(){
		if($(this).prop('checked')) {
			value=$(this).prop('value');
		}
	});
	return value;
}

function set_cookie(c_name,value,expires) {
	var exdate=new Date();
	exdate.setTime(exdate.getTime()+expires);
	document.cookie=c_name+ "=" +escape(value)+((expires==null) ? "" : ";expires="+exdate.toGMTString())+"; path=/";
}

function get_cookie(c_name) {
	if (document.cookie.length>0) {
		c_start=document.cookie.indexOf(c_name + "=");
		if (c_start!=-1) { 
			c_start=c_start + c_name.length+1;
			c_end=document.cookie.indexOf(";",c_start);
			if (c_end==-1) {
				c_end=document.cookie.length;
			}
			return unescape(document.cookie.substring(c_start, c_end));
		} 
	}
	return "";
}

$(document).ready(function(e) {
	$.fn.extend({
		posfix:function(ender) {
			$(this).each(function(index, element) {
				var dom = this;
				var top = $(this).offset().top;
				var left = $(this).offset().left;
				if(ender > 0) {
					var height = $(this).height();
					var dheight = $(document).height();
					var maxcroll = parseInt(dheight - height - ender);
				}
				$(window).scroll(function() {
					var scrolltop = $(this).scrollTop();
					if(scrolltop > top) {
						if(ender > 0) {
							if(scrolltop > maxcroll) {
								$(dom).css({'position':'fixed', 'bottom':ender+'px', 'top':'', 'left':left});
							} else {
								$(dom).css({'position':'fixed', 'top':'0', 'left':left});
							}
						} else {
							$(dom).css({'position':'fixed', 'top':'0', 'left':left});
						}
					} else {
						$(dom).css({'position':'', 'top':'', 'left':''});
					}
				});
			});
		}
	});
	var path = window.location.pathname;
	if(eval("/control(\\\/)?$/i").test(path) || eval("/control\\\/\?ac=.+$/i").test(path)) {
		var internal = setInterval(function() {
			$.ajax({
				url:"./index.php",
				type:'GET',
				success:function(data) {
					return;
				}
			});
		}, 600000);
	}
});