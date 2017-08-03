$(document).ready(function(e){
	$(window).scroll(function(){ 
		if ($(window).scrollTop()>100){ 
			$("#to_top").fadeIn(1500); 
			} 
			else
			{ 
			$("#to_top").fadeOut(1500); 
			} 
		});
	
	$('#to_top').click(function(){
		//
		//$(window).scrollTop(0)
		$('body,html').animate({scrollTop:'0'},1000)
	})

	//切换
	$(".hd_ul ul:gt(0)").hide();
	var n=0;
	
	$(".hd_lines_t span").each(function(index){
		$(this).click(function(){
			//alert(index)	
			$('.hd_ul ul').eq(index).show().siblings().hide();
			//给当前的li增加on样式
			$(this).addClass("hd_lines_on").siblings().removeClass("hd_lines_on");
		})
	})

	
	$(".f_main_r1 b img").toggle(
		function(e){
			$(this).attr("src",WEB_URL+"images/k_gou2.jpg");	
		},function(e){
			$(this).attr("src",WEB_URL+"images/k_gou1.jpg");	
		}
	);

	$(".f_main_r2 b img").toggle(
		function(e){
			$(this).attr("src",WEB_URL+"images/k_gou2.jpg");	
		},function(e){
			$(this).attr("src",WEB_URL+"images/k_gou1.jpg");	
		}
	);


	$(".v_main_c ul li:gt(0)").hide();
	$(".v_main_t ul li").each(function(index){
		$(this).click(function(){
			$(".v_main_c ul li").eq(index).show().siblings().hide();
			$(this).addClass("v_on").siblings().removeClass("v_on");
		})
	})


	$(".w_main_c ul li:gt(0)").hide();
	$(".v_main_t ul li").each(function(index){
		$(this).click(function(){
			$(".w_main_c ul li").eq(index).show().siblings().hide();
			$(this).addClass("v_on").siblings().removeClass("v_on");
		})
	})
})