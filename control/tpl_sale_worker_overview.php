<?php
defined('STCMS_ROOT') or die('Access Deined!');
$thisworker = $mysql_class->select_one("worker", "*", array("id"=>intval($_GET['id'])));
if(empty($thisworker)) {
	halt("系统未找到制定的业务员。");
}

$thisdate = date("Y-m-d");
$enddate = trim($_GET['enddate']);

if(!preg_match("/^20\d{2}\-\d{2}\-\d{2}$/", $enddate) || $enddate > $thisdate) {
	$enddate = $thisdate;
}
$startdate = trim($_GET['startdate']);
if(!preg_match("/^20\d{2}\-\d{2}\-\d{2}$/", $startdate) || $startdate > $enddate) {
	$startdate = date("Y-m-d", strtotime("-7 days", strtotime($enddate)));
}

$tmpoffline = $mysql_class->fetch($mysql_class->query("SELECT SUM(price) AS total_price, COUNT(*) AS total_num FROM {$config['db_prefix']}cardbase WHERE wid='{$thisworker['id']}' AND sell_time >= '{$startdate} 00:00:00' AND sell_time <= '{$enddate} 23:59:59' AND is_use='1'"));
$tmponline = $mysql_class->fetch($mysql_class->query("SELECT SUM(money) AS total_price, COUNT(*) AS total_num FROM {$config['db_prefix']}users_paylog WHERE uid IN (SELECT uid FROM {$config['db_prefix']}users_relation WHERE pid='{$thisworker['uid']}') AND time >= '{$startdate} 00:00:00' AND time <= '{$enddate} 23:59:59' AND ctype='0' AND is_payed='1' AND is_refund='0'"));
$tmpcoin = $mysql_class->fetch($mysql_class->query("SELECT SUM(coin) AS total_coin, COUNT(*) AS total_num FROM {$config['db_prefix']}users_paylog WHERE uid IN (SELECT uid FROM {$config['db_prefix']}users_relation WHERE pid='{$thisworker['uid']}') AND time >= '{$startdate} 00:00:00' AND time <= '{$enddate} 23:59:59' AND ctype='1' AND is_payed='1' AND is_refund='0'"));
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>销售统计__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=sale">销售管理</a>&nbsp;&raquo;&nbsp;<a href="./?ac=sale&do=worker">业务员管理</a>&nbsp;&raquo;&nbsp;销售业绩</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=sale">销售概览</a></li>
				<li class="on"><a href="./?ac=sale&do=worker">业务员管理</a></li>
                <li><a href="./?ac=sale&do=salary">激励机制</a></li>
                <li><a href="./?ac=sale&do=apply">大客户留言</a></li>
				<div class="clear"></div>
			</ul>
		</div>
        <div class="mt10">
			<div class="thead">
				<form id="search-form" method="get" action="./">
					<input type="hidden" name="ac" value="sale" />
                    <input type="hidden" name="do" value="worker" />
                    <input type="hidden" name="step" value="overview" />
                    <input type="hidden" name="id" value="<?php echo $thisworker['id'];?>" />
					起始日期：
					<input class="input" type="text" name="startdate" style="width:100px;" value="<?php echo $startdate; ?>" />
					&nbsp;&nbsp;结束日期：
					<input class="input" type="text" name="enddate" style="width:100px;" value="<?php echo $enddate; ?>" />
					&nbsp;&nbsp;<a href="javascript:void(0)" onclick="$('#search-form').submit();" class="button">搜索</a>
				</form>
			</div>
        </div>
		<div class="mt10">
            <div class="tabcard">
                <div class="menu">
                    <ul>
                        <li class="on"><a href="./?ac=sale&do=worker&step=overview&id=<?php echo $thisworker['id'];?>&startdate=<?php echo $startdate;?>&enddate=<?php echo $enddate;?>">总括</a></li>
						<li><a href="./?ac=sale&do=worker&step=list&id=<?php echo $thisworker['id'];?>&startdate=<?php echo $startdate;?>&enddate=<?php echo $enddate;?>">明细</a></li>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
        </div>
		<div class="mt10">
		<table class="table mt10" bgcolor="#ececec" cellspacing="1" cellpadding="0">
			<tr style=" background:#f1f1f1;">
				<td>线下销售额</td>
				<td>线下订单数</td>
				<td>会员线上现金支付额</td>
				<td>会员线上现金支付订单数</td>
				<td>会员线上积分支付额</td>
				<td>会员线上积分支付订单数</td>
			</tr>
			<tr style="font-weight:bold;">
				<td>¥<?php echo $tmpoffline['total_price'];?></td>
				<td><?php echo $tmpoffline['total_num'];?></td>
				<td>¥<?php echo $tmponline['total_price'];?></td>
				<td><?php echo $tmponline['total_num'];?></td>
				<td><?php echo $tmpcoin['total_coin'];?>积分</td>
				<td><?php echo $tmpcoin['total_num'];?></td>
			</tr>
            <tr>
            	<td colspan="6">
                	<div id="canvasDiv"></div>
                </td>
            </tr>
            <tr>
            	<td colspan="6">
                	<div id="canvasDiv2"></div>
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
	$("select[name=per_page]").val("<?php echo $per_page;?>");
	$("input[name=startdate]").datetimepicker({timepicker:false, format:'Y-m-d'});
	$("input[name=enddate]").datetimepicker({timepicker:false, format:'Y-m-d'});
	
	var data = [
		{name:'线下销售额',value : <?php echo floatval($tmpoffline['total_price']);?>, color:'#cbab4f'},
		{name:'线上现金额',value : <?php echo floatval($tmponline['total_price']);?>, color:'#76a871'},
		{name:'线上积分额',value : <?php echo round(floatval($tmpcoin['total_coin']/100), 2);?>, color:'#c12c44'}
	 ];
	 $(function(){	
		var chart = new iChart.Column2D({
			render : 'canvasDiv',
			data: data,
			title : '<?php echo $startdate;?> 至 <?php echo $enddate;?> 销售额统计',
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
	
	var data2 = [
		{name:'线下订单数',value : <?php echo intval($tmpoffline['total_num']);?>, color:'#cbab4f'},
		{name:'线上现金订单数',value : <?php echo intval($tmponline['total_num']);?>, color:'#76a871'},
		{name:'线上积分订单数',value : <?php echo intval($tmpcoin['total_num']);?>, color:'#c12c44'}
	 ];
	 $(function(){	
		var chart = new iChart.Column2D({
			render : 'canvasDiv2',
			data: data2,
			title : '<?php echo $startdate;?> 至 <?php echo $enddate;?> 订单数统计',
			width : 800,
			height : 400,
			shadow:true,
			shadow_color:'#c7c7c7',
			coordinate:{
				scale:[{
					 position:'left',
					 start_scale:0,
					 end_scale:0,
					 scale_space:0,
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
function go_page(n) {
	$("input[name=page]").val(n);
	$("#search-form").submit();
}
</script>
</body>
</html>