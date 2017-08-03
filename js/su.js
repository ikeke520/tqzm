// JavaScript Document
// STCMS SU JS Packet
// Copyright (C) http://www.phpstcms.com
// Author: Dahongy@gmail.com
var SU = {
	intervalId:'',
	// 输入监听对象
	input_handler:false,
	// 滚屏监听对象
	scroll_handler:{},
	/* 打开浮动对话框
	@param arg 对象
	arg = {title:'对话框标题', url:'对话框页面地址', width:'对话框宽度', height:'对话框高度', msg:'对话框内的HTML代码', cb:function(){点击确定时的回调函数}}
	*/
	dialog:function(arg){
		var title,iscroll,width,height;
		title=typeof(arg.title)=='undefined'?'Notice':arg.title;
		iscroll=typeof(arg.iscroll)=='undefined'?1:arg.iscroll;
		width=arg.width?arg.width:500;
		height=arg.height?arg.height:0;
		if(!SU.g('_dialog_background')) {
			var bgobj=SU.ce('div');
			bgobj.setAttribute('id', '_dialog_background');
			SU.ap(bgobj);
		} else {
			var bgobj = SU.g('_dialog_background');
		}
		bgobj.style.backgroundColor='#FFF';
		bgobj.style.position='absolute';
		SU.set_pos('_dialog_background', {left:'0px',top:'0px'});
		bgobj.style.height=SU.get_wht('ph') +'px';
		bgobj.style.width=SU.get_wht('bow')+'px';
		if(!SU.g('_dialog_box')) {
			var dialog_obj = SU.ce('div');
			dialog_obj.setAttribute('id', '_dialog_box');
			SU.ap(dialog_obj);
		} else {
			var dialog_obj = SU.g('_dialog_box');
		}
		dialog_obj.style.position='absolute';
		var set_top = SU.get_wht('boh')/8 > 150 ? 150 : SU.get_wht('boh')/8;
		SU.set_pos('_dialog_box', {left:(SU.get_wht('bow')-width)/2+'px', top:parseInt(set_top+SU.get_wht('st'))+'px'});
		dialog_obj.className='dialog';
		dialog_obj.style.width=width+'px';
		dialog_obj.style.minHeight=height+'px';
		html='';
		html += '<div id="_dialog_title" class="title"><span id="_dialog_close" class="close">　</span>'+title+'</div>'
		if(arg.msg) {
			html += '<div id="_dialog_content" class="content"><div class="msg">'+arg.msg+'</div></div>';
			if(arg.cb) {
				html += '<div class="bottom"><input type="button" class="confirm" onclick="SU.run('+arg.cb+')" value="&nbsp;确定&nbsp;" /> <input class="cancle" type="button" onclick="SU.rd();" value="&nbsp;取消&nbsp;" />';
			}
		} else if(arg.url) {
			html += '<div id="_dialog_content" class="content"><iframe id="_dialog_frame" src="" class="frame" frameborder="no" scrolling="auto" width="'+ (width-12) +'" height="'+(height-50)+'"></iframe></div>'
		}
		dialog_obj.innerHTML = html;
		if(arg.url){
			$('#_dialog_frame').attr('src', arg.url)
			SU.sih(true);
		}
		if(iscroll) SU.set_scroll('_dialog_box');
		$('#_dialog_background').fadeTo(0, 0.7);
		$('#_dialog_close').bind('click', SU.rd);
		SU.drag('_dialog_title', '_dialog_box', iscroll, arg.disa, arg.disv);
	},
	// 浮动对话框高度自适应函数
	sih:function(bool) {
		if(!SU.g('_dialog_frame')) {
			return false;
		}
		var iframe_height = 0;
		var win = SU.g('_dialog_frame').contentWindow;
		if(win.document.readyState!='complete' || win.location.href=='about:blank') {
			setTimeout(function(){SU.sih(true)}, 100);
			return false;
		}
		if(typeof win.window.SU != 'undefined') {
			iframe_height = win.window.SU.get_wht('ph');
		} else {
			if(win.document.body.scrollHeight>=win.document.body.clientHeight) {
				iframe_height = win.document.body.scrollHeight;
			} else {
				iframe_height = win.document.body.clientHeight;
			}
		}
		win.window.document.body.onclick=function(){setTimeout(function(){try{SU.sih(false);}catch(e){}}, 100)}
		if(iframe_height>0) {
			if(iframe_height>SU.get_wht('boh')-100) {
				iframe_height = SU.get_wht('boh')-100;
			}
			if((SU.get_wht('boh') - iframe_height - 100)/2 < SU.get_wht('boh')/8 && bool) {
				SU.set_pos('_dialog_box', 'top', (SU.get_wht('boh') - iframe_height - 100)/2 + SU.get_wht('st')+'px');
			}
			SU.g('_dialog_frame').height = iframe_height;
			$('#_dialog_loading').remove();
		}
	},
	/* 设定对象可以拽托
	@param lsn_id 字符串 设定拽托事件的监控对象
	@param tgt_id 字符串 设定拽托的受体
	@param iscroll 布尔值 是否随滚动条自动浮动
	@param disa 布尔值 是否取消水平方向上的拽托
	@param disv 布尔值 是否取消垂直方向上的拽托
	*/
	drag:function(lsn_id, tgt_id, iscroll, disa, disv){
		if(!tgt_id) tgt_id=lsn_id; if(typeof iscroll =='undefined') iscroll=1;
		var data={left:0,top:0,old_x:0,old_y:0,new_x:0,new_y:0,dragable:false};
		var mousedown=function(event) {
			data.left=SU.get_pos(tgt_id, 'left');
			data.top=SU.get_pos(tgt_id, 'top');
			data.old_x=event.clientX;
			data.old_y=event.clientY;
			data.dragable=true;
			if(iscroll) SU.lock_scroll(tgt_id,'lock');
			return false
		};
		var mousemove=function(event){
			if(data.dragable) {
				data.new_x=event.clientX;
				data.new_y=event.clientY;
				if(!disa)SU.set_pos(tgt_id, 'left', (data.new_x - data.old_x + data.left)+'px');
				if(!disv)SU.set_pos(tgt_id, 'top', (data.new_y - data.old_y + data.top)+'px');
			}
			return false;
		};
		var mouseup=function() {
			data.dragable=false;
			if(iscroll) {
				SU.lock_scroll(tgt_id, 'unlock');
				SU.set_scroll(tgt_id);
			}
			return false
		};
		var dblclick=function() {
			SU.rd();
		};
		var mouseout=function() {
			data.dragable=false;
			if(iscroll) {
				SU.lock_scroll(tgt_id, 'unlock');
				SU.set_scroll(tgt_id);
			}
			return false
		};
		$('#'+lsn_id).bind({
			mousedown:mousedown,
			mousemove:mousemove,
			mouseup:mouseup,
			mouseout:mouseout,
			dblclick:dblclick
		}).css('cursor', 'move');
		if(iscroll) {
			SU.lock_scroll(tgt_id, 'unlock');
			SU.set_scroll(tgt_id);
		}
		return false;
	},
	// 设置一个HTML元素随滚动条而浮动
	set_scroll:function(id) {
		//support multiple document elements
		SU.scroll_handler[id] = {top:SU.get_pos(id, 'top'), old_y:SU.get_wht('st'), new_y:0};
		var scroll_move=function() {
			for (i in SU.scroll_handler) {
				if($('#'+i).attr('scroll')=='unlock') {
					SU.scroll_handler[i].new_y=SU.get_wht('st');
					SU.set_pos(i, 'top', (SU.scroll_handler[i].top + SU.scroll_handler[i].new_y - SU.scroll_handler[i].old_y)+'px');
				}
			}
		}
		window.onscroll=scroll_move;
	},
	/* 浮动提示信息
	@param msg HTML代码 提示信息内容
	@param auto_hide 布尔值 是否自动关闭
	*/
	tip:function(msg, auto_hide) {
		auto_hide = typeof(auto_hide)=='undefined' ? true : false;
		if(!SU.g('_tip')) {
			tip_obj = SU.ce('div');
			tip_obj.setAttribute('id', '_tip');
		} else {
			tip_obj = SU.g('_tip');
		}
		tip_obj.className = 'tip';
		tip_obj.innerHTML='<span class="left"></span><span class="center">'+msg+'</span><span class="right"></span>';
		SU.ap(tip_obj);
		SU.set_pos('_tip',{left:((SU.get_wht('bow') - $('#_tip').width())/2) +'px', top:SU.get_wht('boh')/2 - SU.get_wht('boh')*0.2 + SU.get_wht('st') + 'px'});
		if(auto_hide) setTimeout(function(){SU.hide_tip()}, 1500);
	},
	hide_tip:function() {
		try {SU.rm(SU.g('_tip'));}catch(e){}
 	},
	// 图片预览
	// @param dom HTML对象 要监控的事件元素
	// @param src 字符串 图片地址
	imgpreview:function(dom, src) {
		var data = {left:0, top:0, old_x:0, old_y:0, new_x:0, new_y:0, moveable:false};
		var divobj = SU.ce('span');
		divobj.setAttribute('id', '_img_preview_span');
		divobj.style.position='absolute';
		divobj.style.padding='3px';
		divobj.style.border='#666 solid 1px';
		divobj.style.background='#ddd';
		divobj.innerHTML='<img  src='+src+' />';
		var mouseover = function(event) {
			data.left = event.clientX+10;
			data.top = event.clientY+10;
			data.old_x = event.clientX;
			data.old_y = event.clientY;
			SU.ap(divobj);
			SU.set_pos('_img_preview_span', {left:data.left + 'px', top:data.top + 'px'});
			data.moveable = true;
		}
		var mousemove = function(event) {
			if(data.moveable) {
				data.new_x = event.clientX;
				data.new_y = event.clientY;
				SU.set_pos('_img_preview_span', {left:(data.left + data.new_x - data.old_x)+'px', top:(data.top + data.new_y - data.old_y + SU.get_wht('st'))+'px'});
			}
		}
		var mouseout = function(event) {
			data = {left:0, top:0, old_x:0, old_y:0, new_x:0, new_y:0, moveable:false};
			SU.rm(divobj);
		}
		$(dom).bind({
			'mouseover':mouseover,
			'mousemove':mousemove,
			'mouseout':mouseout
		});
	},
	// 图片放大
	// @param src 字符串 图片的地址
	zoompic:function(src) {
		if(!SU.g('_zoompic')) {
			var obj = SU.ce('span');
			obj.setAttribute('id', '_zoompic');
		} else {
			var obj = SU.g('_zoompic');
		}
		obj.style.position = 'absolute';
		obj.setAttribute('class', 'zoompic');
		SU.ap(obj);
		var html = '<img id="_zoompic_img" onclick="SU.hide_zoompic();" src="'+src+'" /><a class="close" onclick="SU.hide_zoompic();" title="关闭">&nbsp;&nbsp;&nbsp;&nbsp;</a>';
		obj.innerHTML = html;
		SU.set_pos('_zoompic', {left:(SU.get_wht('bow')-$('#_zoompic').outerWidth())/2 + 'px', top:(SU.get_wht('boh')/8+SU.get_wht('st')) + 'px'});
		//SU.drag('_zoompic_img', '_zoompic', false);
	},
	// 隐藏放大图片
	hide_zoompic:function() {
		return SU.rm(SU.g('_zoompic'));
	},
	// 监听输入
	// @param dom HTML对象 监听事件对象
	// @param func 函数对象 处理监听的函数
	input_listen:function(dom, func) {
		if(!$(dom)[0]) {
			return false;
		}
		if("\v"=="v") {
			$(dom)[0].attachEvent('onpropertychange', func);
		} else {
			$(dom)[0].addEventListener('input', func, false);
			$(dom)[0].addEventListener('change', func, false);
			$(dom)[0].addEventListener('blur', func, false);
		}
	},
	// 释放监听输入
	input_release:function(dom, func) {
		if(!$(dom)[0]) {
			return false;
		}
		if("\v"=="v") {
			$(dom)[0].detachEvent('onpropertychange', func);
		} else {
			$(dom)[0].removeEventListener('input', func, false);
			$(dom)[0].removeEventListener('change', func, false);
			$(dom)[0].removeEventListener('blur', func, false);
		}
	},
	// 锁定滚动
	lock_scroll:function(id,value) {
		$('#'+id).attr('scroll',value);
	},
	// 取得宽度、高度、滚动条长度、
	// @param a 字符串 取得的类型
	get_wht:function(a) {
		var wht = {
			bow:parseInt(document.body.clientWidth+0),	//body offset width
			boh:parseInt(boh()),	//body offset height
			sw:parseInt(screen.width+0),	//screen width
			sh:parseInt(screen.height+0),	//screen height
			st:parseInt(get_st()),	//scrollTop
			ph:get_ph()	//page height
		};
		if(a) {
			return wht[a];
		} else {
			return wht;
		}
		function get_ph() {
			if(document.body.scrollHeight>=document.body.clientHeight) {
				return document.body.scrollHeight;
			} else {
				return document.body.clientHeight;
			}
		}
		function get_st() {
			if (self.pageYOffset) {
				var st = self.pageYOffset;
			} else if (document.documentElement && document.documentElement.scrollTop) {
				var st = document.documentElement.scrollTop;
			} else {
				var st = document.body.scrollTop;
			}
			return st;
		}
		function boh() {
			var pageHeight = window.innerHeight;
			if (typeof pageWidth != "number"){
				if (document.compatMode == "CSS1Compat"){
					pageHeight = document.documentElement.clientHeight;
				} else{
					pageHeight = document.body.clientHeight;
				}
			}
			return pageHeight;		
		}
	},
	// 取得元素的位置
	// @param id 字符串 元素id属性
	// @param it 字符串 位置的类型，可取left和top
	get_pos:function(id,it) {
		var pos = $('#'+id).position();
		if(it) {
			return pos[it];
		} else {
			return pos;
		}
	},
	// 设置元素的位置
	// @param id 字符串 元素id属性
	// @param it 字符串 位置的类型，可取left和top
	// @param value 字符串 位置值
	set_pos:function(id, it, value) {
		var p = SU.g(id);
		if(!value) {
			p.style.left=it['left'];
			p.style.top=it['top'];
		} else {
			p.style[it]=value;
		}
	},
	// 取得HTML对象
	// @param id 字符串 元素id属性
	g:function(id) {
		return document.getElementById(id);
	},
	// 创建一个元素
	// @param n 字符串 标签名称
	ce:function(n) {
		return document.createElement(n);
	},
	// 在body中添加一个HTML对象
	// @param o HTML对象 要添加的HTML对象
	ap:function(o) {
		return document.body.appendChild(o);
	},
	// 在body中移除一个HTML对象
	// @param o HTML对象 要移除的HTML对象
	rm:function(o) {
		try{ document.body.removeChild(o);} catch(e) {}
	},
	// 移除浮动对话框
	rd:function() {
		SU.rm(SU.g('_dialog_background'));
		SU.rm(SU.g('_dialog_box'));
	},
	// 运行函数
	// @param func 函数对象 要云顶的函数
	run:function(func) {
		return func();
	}
}
