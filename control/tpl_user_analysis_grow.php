<?php
defined('STCMS_ROOT') or die('Access Deined!');
$thismonth = date("Y-m");
$month = trim($_GET['month']);
if(!preg_match("/^20\d{2}\-\d{2}$/", $month)) {
	$month = $thismonth;
}
$records = $mysql_class->fetch_all("SELECT COUNT(*) AS num, DATE_FORMAT(reg_time, '%Y-%m-%d') AS day, DATE_FORMAT(reg_time, '%d') AS name FROM {$config['db_prefix']}users WHERE reg_time >= '{$month}-01 00:00:00' AND reg_time <= '{$month}-31 23:59:59' GROUP by day ORDER BY day ASC");
if($records) {
	$max = max(get_id_array($records, "num"));
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>增加分析__后台管理中心__<?php echo $config['seo_title'];?></title>
<link href="../css/control.css" type="text/css" rel="stylesheet">
<link href="../js/jquery.datetimepicker.css" type="text/css" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0"/>
</head>
<body>
<div class="top">
	<div class="wrapper">
		<div class="logo"><?php echo $config['seo_title'];?> 后台管理中心</div>
		<div class="link"> <a href="../" target="_blank">网站首页</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="./?ac=logout">退出登录</a> </div>
	</div>
</div>
<div class="nav">
	<dl>
		<dt class="activity<?php if($thismodel == "activity"){?> on<?php }?>"><a href="./">活动管理</a></dt>
		<dt class="sale<?php if($thismodel == "sale"){?> on<?php }?>"><a href="./?ac=sale">销售管理</a></dt>
		<dt class="card<?php if($thismodel == "card"){?> on<?php }?>"><a href="./?ac=card">亲子卡管理</a></dt>
		<dt class="news<?php if($thismodel == "news"){?> on<?php }?>"><a href="./?ac=news">资讯管理</a></dt>
		<dt class="order<?php if($thismodel == "order"){?> on<?php }?>"><a href="./?ac=order">财务订单</a></dt>
		<dt class="user<?php if($thismodel == "user"){?> on<?php }?>"><a href="./?ac=user">会员管理</a></dt>
		<dt class="connect<?php if($thismodel == "connect"){?> on<?php }?>"><a href="./?ac=connect">微信接口</a></dt>
		<dt class="ads<?php if($thismodel == "ads"){?> on<?php }?>"><a href="./?ac=ads">广告管理</a></dt>
		<dt class="setting<?php if($thismodel == "setting"){?> on<?php }?>"><a href="./?ac=setting">系统设置</a></dt>
	</dl>
</div>
<div class="main">
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=user">用户管理</a>&nbsp;&raquo;&nbsp;<a href="./?ac=user&do=analysis">用户分析</a>&nbsp;&raquo;&nbsp;增长分析</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=user">用户列表</a></li>
				<li class="on"><a href="./?ac=user&do=analysis">用户分析</a></li>
                <li><a href="./?ac=user&do=crm">CRM管理</a></li>
                <li><a href="./?ac=sale&do=index&step=rank">会员消费排行</a></li>
			</ul>
		</div>
		<div class="mt10">
            <div class="tabcard">
                <div class="menu">
                    <ul>
                        <li><a href="./?ac=user&do=analysis">地区分析</a></li>
                        <li class="on"><a href="./?ac=user&do=analysis&step=grow">增长分析</a></li>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
        </div>
		<div class="mt10">
            <table class="table mt10" bgcolor="#ececec" cellspacing="1" cellpadding="0">
                <tr style=" background:#f1f1f1;">
                    <td>
                 设置统计月份：<input type="text" name="month" style="width:100px;" value="<?php $month;?>" class="input">&nbsp;&nbsp; <a class="button" href="javascript:" onClick="setmonth();"><span>设置</span></a>
                   
                    </td>
                </tr>
                <tr>
                    <td>
                        <div id="canvasDiv"></div>
                    </td>
                </tr>
            </table>
				
		</div>
	</div>
</div>
<div class="footer">
	<div class="copyright">
		<div class="wapper"> 
			<p>版权所有 &copy <?php echo $config['seo_title'];?>。未经允许，任何人不得使用、复制、二次开发。</p>
			<p>系统开发：<a href="http://www.phpstcms.com/" target="_blank">STCMS</a>，me@yangdahong.cn</p>
		</div>
	</div>
</div>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/su.js"></script>
<script type="text/javascript" src="../js/jquery.datetimepicker.js"></script>
<script type="text/javascript" src="../js/icharts.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {
	$("input[name=month]").datetimepicker({timepicker:false, format:'Y-m'});
	var data = [
<?php
if($records) {
	foreach($records as $key => $list) {
?>
		<?php if($key != 0){?>,<?php }?>{name:'<?php echo $list['name'];?>',value : <?php echo $list['num'];?>, color:'#a5c2d5'}
<?php
	}
}
?>
	 ];
	 $(function(){	
		var chart = new iChart.Column2D({
			render : 'canvasDiv',
			data: data,
			title : '<?php echo $month;?> 月用户增长情况（未有新增用户的日期不显示）',
			width : 800,
			height : 400,
			shadow:true,
			shadow_color:'#c7c7c7',
			coordinate:{
				scale:[{
					 position:'left',
					 start_scale:0,
					 end_scale:<?php echo ceil($max/0.8);?>,
					 scale_space:<?php echo round($max/4);?>,
					 listeners:{
						parseText:function(t,x,y){
							return {text:t}
						}
					}
				}]
			}
		});
		chart.draw();
	});
});
function setmonth() {
	if(/^20\d{2}\-\d{2}$/.test($("input[name=month]").val())) {
		location.href = "./?ac=user&do=analysis&step=grow&month="+$("input[name=month]").val();
	} else {
		SU.tip("请输入xxxx-xx格式的月份。");
	}
}
</script>
</body>
</html>