<?php
include("../lib/include.php");
include(STCMS_ROOT."wxconfig.php");
$thisuser = get_user();
$wxjsapi = get_wxsign();
if($_GET['ac'] == "list") {
	include("./list.php");
	exit;
}
$events = $mysql_class->fetch_all("SELECT date, COUNT(*) AS num FROM {$config['db_prefix']}activity WHERE is_complete='1' GROUP BY date");
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0;">
<title><?php echo $config['seo_title'];?></title>
<meta name="keywords" content="<?php echo $config['seo_keyword'];?>">
<meta name="description" content="<?php echo $config['seo_desc'];?>">
<link rel="stylesheet" type="text/css" href="<?php echo check_dir($config['web_url']);?>css/style.css">
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/common.css" />
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/fullcalendar.css" />
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/fullcalendar.print.css" />
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/weui.min.css" />
<style>
#calendar {max-width: 680px;margin: 0 auto;}
.fc-prev-button{display: none;}
#bk {position: absolute;top:3.9%;left: 10%; border-radius: 3px;background: none;color: #222222; z-index: 99999;}
</style>
</head>
<body style="background:#fff">
	<div class="a_footer">
		<ul>
			<li><a href="<?php echo check_dir($config['web_url']);?>"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer1.jpg"><span>首页</span></a></li>
			<li class="a_li1"><a href="<?php echo check_dir($config['web_url']);?>d/"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer6.jpg"><span>活动日历</span></a></li>
			<li><a href="<?php echo check_dir($config['web_url']);?>c/"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer3.jpg"><span>童趣卡</span></a></li>
			<li><a href="<?php echo check_dir($config['web_url']);?>u/"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer4.jpg"><span>我的</span></a></li>
		</ul>
	</div>
    <a href="javascript:;" id="bk">回到当月</a>
    <div class="active_canlend">
        <div id='calendar'></div>
    </div>
    <div class="weui_loading_toast" style="display:none;">
       <div class="weui_mask_transparent"></div>
       <div class="weui_toast">
           <div class="weui_loading">
               <div class="weui_loading_leaf weui_loading_leaf_0"></div>
               <div class="weui_loading_leaf weui_loading_leaf_1"></div>
               <div class="weui_loading_leaf weui_loading_leaf_2"></div>
               <div class="weui_loading_leaf weui_loading_leaf_3"></div>
               <div class="weui_loading_leaf weui_loading_leaf_4"></div>
               <div class="weui_loading_leaf weui_loading_leaf_5"></div>
               <div class="weui_loading_leaf weui_loading_leaf_6"></div>
               <div class="weui_loading_leaf weui_loading_leaf_7"></div>
               <div class="weui_loading_leaf weui_loading_leaf_8"></div>
               <div class="weui_loading_leaf weui_loading_leaf_9"></div>
               <div class="weui_loading_leaf weui_loading_leaf_10"></div>
               <div class="weui_loading_leaf weui_loading_leaf_11"></div>
           </div>
           <p class="weui_toast_content">数据加载中</p>
       </div>
    </div>
    <div class="hidden"><img src="<?php echo check_dir($config['web_url']);?>images/share.jpg"></div>
    <div class="hidden weui_dialog_alert">
        <div class="weui_mask"></div>
        <div class="weui_dialog">
            <div class="weui_dialog_hd"><strong class="weui_dialog_title" style="font-size:14px;">您还未关注，将无法使用某些功能</strong></div>
            <div class="weui_dialog_bd">
                <img style="width:200px;" src="<?php echo check_dir($config['web_url']);?>images/qrcode.jpg">
            </div>
            <div class="weui_dialog_ft">
                <a href="javascript:" onClick="$('.weui_dialog_alert').hide();" class="weui_btn_dialog primary">长按二维码关注微信公众号</a>
            </div>
        </div>
    </div>
</body>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/fullcalendar.min.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/jquery.touchswipe.min.js"></script>
<script type="text/javascript">
wx.config({
    debug: false,
    appId: "<?php echo WXAPPID;?>",
    timestamp: "<?php echo $wxjsapi['timestamp'];?>",
    nonceStr: "<?php echo $wxjsapi['noncestr'];?>",
    signature: "<?php echo $wxjsapi['signature'];?>",
    jsApiList: ["startRecord", "stopRecord", "onVoiceRecordEnd", "playVoice", "pauseVoice", "stopVoice",
		"onVoicePlayEnd", "uploadVoice", "downloadVoice", "chooseImage", "previewImage", "uploadImage",
		"downloadImage", "getNetworkType", "chooseWXPay", "onMenuShareTimeline", "onMenuShareAppMessage"]
});
wx.ready(function() {
	wx.onMenuShareTimeline({
		title:document.title,
		link:location.href,
		imgUrl:"<?php echo check_dir($config['web_url']);?>images/share.jpg"
	});
	wx.onMenuShareAppMessage({
		title:document.title,
		link:location.href,
		imgUrl:"<?php echo check_dir($config['web_url']);?>images/share.jpg",
		desc:"<?php echo $config['seo_desc'];?>"	});
});
$(document).ready(function(e) {
    if("<?php echo $thisuser['is_reg'];?>" === "0") {
		$(".weui_dialog_alert").show();
	}
	$('#calendar').fullCalendar({
		eventLimit: true, // allow "more" link when too many events
		weekMode:'variable',
		editable: true,
	
		monthNames: ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'],
		monthNamesShort: ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'],
		dayNames: ['日','一','二','三','四','五','六'],
		dayNamesShort: ['日','一','二','三','四','五','六'],
		header: {
			left:'',		  
			center: 'prev,title,next',
			right:'',
		},
		height:'auto',
		
		events: [
<?php
if($events) {
	foreach($events as $list) {
?>			
			{
				title: "<?php echo $list['num'];?>场活动",
				start: "<?php echo $list['date'];?>",							
				url:"<?php echo check_dir($config['web_url']);?>d/?ac=list&date=<?php echo $list['date'];?>"
			},
<?php
	}
}
?>
		]

	});
	//左右滑动日历
	$(".active_canlend").swipe( {
		swipeLeft:function(event, direction, distance, duration, fingerCount) {
			$('#calendar').fullCalendar('next');
			return false;
		},
		swipeRight:function(event, direction, distance, duration, fingerCount) {
			$('#calendar').fullCalendar('prev');
			return false;
		}
	});
	function fixnumber(n) {
		return (n > 9 ? n : '0'+n);
	}
	$("#bk").click(function(){
		var curDate=new Date();
		var y=curDate.getFullYear();
		var m=curDate.getMonth()+1;
		var r=curDate.getDate();
		var currentDate=y+'-'+fixnumber(m)+'-'+fixnumber(r);
		$('#calendar').fullCalendar('gotoDate', currentDate);
	})
});

</script>
</html>