<?php
header("content-Type: text/html; charset=utf-8");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ob_start();
$runtime= new runtime;$runtime->start();class runtime{var $StartTime = 0;var $StopTime = 0;function get_microtime(){list($usec,$sec)=explode(' ',microtime());return((float)$usec+(float)$sec);}function start(){$this->StartTime=$this->get_microtime();}function stop(){$this->StopTime=$this->get_microtime();}function spent(){return round(($this->StopTime-$this->StartTime),6);}}

//********************
//		设置区开始
//********************
//认证 KEY
$zKey = "test";
//刷新间隔 单位:毫秒 1秒=1000毫秒 
$zSec = "1000";
//404页面
$zError = "http://www.htooy.org/404.html";
//服务器列表
$zUrls = array(
"http://127.0.0.1/status.php",
"http://127.0.0.1/status.php",
);

//系统资源预警值提示信息
//提示信息请尽量简短
//一分钟负载
$zOLT = "一分钟负载过高";
//五分钟负载
$zFLT = "五分钟负载过高";
//十五分钟负载
$zFtLT = "十五分钟负载过高";
//磁盘使用率
$zDPT = "该清理磁盘空间啦~~";
//物理内存使用率
$zMRPT = "物理内存占用过高啦~~";
//SWAP内存使用率
$zSPT = "SWAP已使用过半了,你不觉得卡嘛~~";
//********************
//		设置区结束
//********************

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="zh-CN">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="keywords" content="php,sysinfo,ajax,无刷新,实时,系统信息," />
<meta name="description" content="使用json+ajax实现的系统信息无刷新实时显示,可查看磁盘,内存,负载,系统时间,mysql状态等信息" />
<meta name="generator" content="PHP-SYSINFO" />
<meta name="author" content="LadonQ" />
<meta name="copyright" content="HTOOY.ORG" />
<script type="text/javascript" src="jquery.min.js"></script>
<style type="text/css"> 
<!--
* {margin:0;padding:0;border:none;}
li{list-style:none;}
body{font-size:14px;font-family:Tahoma,verdana;line-height:22px;}
a{color:blue;text-decoration:none;}
a:hover{text-decoration:underline;}
#info em{color:#FF9D25;}
#info strong a{color:red;}
#copyright strong a{color:#000;}
h1{text-align:center;line-height:60px;border-bottom:1px solid #C9D7F1;margin-bottom:20px;}
h2{border:0px;text-align:center;line-height:40px;}
h2 a{float:right;font-weight:normal;font-size:12px;}
h3,#footer{padding:4px 5px;text-align:left;background-color:#f0f7ff;border:1px solid #C9D7F1;}
#footer{font-size:10px;font-family:Tahoma,verdana;}
#main{width:720px;margin:0px auto;}
.notice-wrap{position:fixed;bottom:20px;left:20px;width:250px;z-index:9999;}
* html .notice-wrap{position:absolute;}
.notice-item{background:#333;-moz-border-radius:8px;-webkit-border-radius:8px;color:#eee;padding:12px;font-family:Arial,Helvetica,sans-serif;font-size:13px;display:block;position:relative;margin:0 0 12px 0;}
.notice-item-close{position:absolute;font-family:Arial;font-size:12px;font-weight:bold;right:6px;top:6px;cursor:pointer;}
.success{background-color:#090;}
.error{background-color:#900;}
.scs{text-align:center;}
.clearDiv{clear:both;}
.infoList ul li{background-color:#F0F7FF;border:1px solid #C9D7F1;float:left;line-height:30px;margin:0px 6px 10px;padding:6px 10px;width:325px;}
.infoList ul li:hover{background-color:#fff;}
.statusRed{color:#f00;font-weight:bold;}
.statusGreen{color:#090;font-weight:bold;}
.infoItem{margin-bottom:10px;}
.infoItemDiv{padding:5px;margin-left:15px;}
#ServerSpace,#ServerLoadAvg,#ServerMemory,#ServerSwap,#ServerTime,#ServerMysql,#ServerNetWorks,#ServerNetN,
#ServerCONNNum,#ServerCONNStatus,#ServerCONNIP,
#ServerTCP4N,#ServerTCP6N,#ServerUDP4N,#ServerUDP6N,
#ServT4ST,#ServU4ST,#ServT6ST,#ServU6ST,
#ServerTCP4IP,#ServerTCP6IP,#ServerUDP4IP,#ServerUDP6IP
{display:none;}
-->
</style>
<?php
//VerChk
$zVersion = "1.0";
$zVers = @file_get_contents("http://htooy.googlecode.com/svn/trunk/ver/ajaxsysinfo/verchk.txt");
if (empty($zVers))$zVers = @file_get_contents("http://ver.htooy.org/ajaxsysinfo/verchk.txt");
if (empty($zVers))$zVers = null;
if (empty($zVers)){$zNewVer = "<br />";
}else{$zVer = explode("|",$zVers);
	if ($zVer['0']<=$zVersion){$zNewVer = "<br />";
	}else{$zNewVer = " | <strong><a href=\"".$zVer['1']."\" rel=\"external\">The latest version ".$zVer['0']."</a></strong><br />";}
	if (empty($zVer['2'])){$zMsg = null;}else{$zMsg = "<span id=\"msg\">".$zVer['2']."</span><br />";}
}
//Auth
$zNum = $_GET['num'];
$zCurls = count($zUrls);
if (($zNum!==null)&&($zNum>="0")&&($zNum<$zCurls))
{
	$zUrl = $zUrls[$zNum]."?ip=".$_SERVER["REMOTE_ADDR"];
	$zKey = md5($zKey);
	$zKey = md5(getenv("SERVER_PROTOCOL")."+".$_SERVER["REMOTE_ADDR"]."+".$_SERVER["SERVER_PORT"]."+".getenv("REQUEST_METHOD")."+".$zKey);
	$zVN = @file_get_contents($zUrl."&key=key");
	if ($zKey !== $zVN)
	{
		header("HTTP/1.1 404 Not Found");
		header("Location: $zError");
		exit;
	}
	$zST = explode("|",@file_get_contents($zUrl."&st=status"));
	$zSQL = $zST['0'];$zNet = $zST['1'];$zNetNum = $zST['2'];
	$zTCP4N = $zST['3'];$zUDP4N = $zST['6'];$zTCP6N = $zST['9'];$zUDP6N = $zST['12'];
	$zTCP4IP = $zST['4'];$zUDP4IP = $zST['7'];$zTCP6IP = $zST['10'];$zUDP6IP = $zST['13'];
	$zTCP4ST = $zST['5'];$zUDP4ST = $zST['8'];$zTCP6ST = $zST['11'];$zUDP6ST = $zST['14'];
	$zOL = $zST['15'];$zFL = $zST['16'];$zFtL = $zST['17'];$zDP = $zST['18'];$zMRP = $zST['19'];$zSP = $zST['20'];
	$zUrl = $zUrl."&num=".$zNum."&act=".$zKey;
}else{?>
<title>服务器列表</title>
</head>
<body>
<div id="main">
	<h1>请选择要查看的服务器</h1>
	<div id="ServerList" class="infoList">
		<ul>
<?php
for ($n=0;$n<$zCurls;++$n)
{
	$zCode = get_http_response_code($zUrls[$n]);
	if ($zCode !== "200")
	{
		echo '<li><b>服务器 [',$n+1,']</b><br />服务器状态 : <span class="statusRed">超时-',$zCode;
	}else{
		echo '<li><a href="?num=',$n,'"><b>服务器 [',$n+1,']</b></a><br />服务器状态 : <span class="statusGreen">正常-',$zCode;
	}
    echo '</span></li>';
}
?>
    	</ul>
    	<div class="clearDiv"></div>
	</div>
	<div id="footer">
		<span id="info">
			<?php
				echo $zMsg,'CopyLeft ',date("Y",time()),' , Powered by <strong>PHP-SYSINFO</strong> <em>v',$zVersion,'</em> .',$zNewVer;
				$runtime->stop();
				echo 'Processed in ',$runtime->spent(),' second(s). Memory Usage ',zFS(memory_get_usage()),'.';
			?>
		</span><br />
		<span id="copyright">
			&copy; CopyRight 2004, <strong><a href="http://www.htooy.org/" rel="external">HTOOY.ORG</a></strong> Inc.All Rights Reserved. | 
			<strong><a href="http://validator.w3.org/check?uri=referer" rel="external">Valid XHTML 1.0 Strict</a></strong> | 
			<strong><a href="http://jigsaw.w3.org/css-validator/check/referer" rel="external">Valid CSS</a></strong>
		</span>
	</div>
	<div class="clearDiv"></div>
</div>
</body>
</html>
<?php exit;}?>
<title>服务器状态</title>
</head>
<body>
<div id="main">
	<h2><a href="monitor.php">返回服务器列表</a>服务器[<span id="num">[n/a]</span>]-[<span id="IP">[n/a]</span>] 的状态</h2>
	<!-- Server Info here -->
	<div id="ServerInfo" class="infoItem">
		<h3>服务器信息</h3>
		<div id="ServerTime" class="infoItemDiv">
			<b>时间</b>
			<table>
				<tr><td>开机时间 : </td><td><span id="BTime">[n/a]</span></td></tr>
				<tr><td>运行时间 : </td><td><span id="UpTime">[n/a]</span></td></tr>
				<tr><td>空闲时间 : </td><td><span id="FreeTime">[n/a]</span></td></tr>
				<tr><td>服务器时间 : </td><td><span id="SysTime">[n/a]</span></td></tr>
				<tr><td>北京时间 : </td><td><span id="BJTime">[n/a]</span></td></tr>
			</table>
		</div>
		<div id="ServerLoadAvg" class="infoItemDiv">
			<b>服务器负载详情</b>
			<table>
				<tr><td>一分钟负载 : </td><td><span id="OneLoad">[n/a]</span></td></tr>
				<tr><td>五分钟负载 : </td><td><span id="FiveLoad">[n/a]</span></td></tr>
				<tr><td>十五分钟负载 : </td><td><span id="FifteenLoad">[n/a]</span></td></tr>
				<tr><td>进程 : </td><td><span id="ProcLoad">[n/a]</span></td></tr>
			</table>
		</div>
		<div id="ServerSpace" class="infoItemDiv">
			<b>硬盘空间详情</b>
			<table>
				<tr><td>剩余空间 : </td><td><span id="FreeSpace">[n/a]</span></td></tr>
				<tr><td>总计空间 : </td><td><span id="TotalSpace">[n/a]</span></td></tr>
				<tr><td>空间使用率 : </td><td><span id="PercentSpace">[n/a]</span></td></tr>
			</table>
		</div>
		<div id="ServerMemory" class="infoItemDiv">
			<b>物理内存详情</b>
			<table>
				<tr><td>总计内存 : </td><td><span id="TotalMemory">[n/a]</span></td></tr>
				<tr><td>已用内存 : </td><td><span id="UsedMemory">[n/a]</span></td></tr>
				<tr><td>缓冲区使用 : </td><td><span id="CachedMemory">[n/a]</span></td></tr>
				<tr><td>空闲内存 : </td><td><span id="FreeMemory">[n/a]</span></td></tr>
				<tr><td>内存使用率 : </td><td><span id="PercentMemory">[n/a]</span></td></tr>
				<tr><td>真实已用内存 : </td><td><span id="RealUsedMemory">[n/a]</span></td></tr>
				<tr><td>真实内存使用率 : </td><td><span id="RealPercentMemory">[n/a]</span></td></tr>

			</table>
		</div>
		<div id="ServerSwap" class="infoItemDiv">
			<b>Swap内存详情</b>
			<table>
				<tr><td>总计Swap内存 : </td><td><span id="TotalSwap">[n/a]</span></td></tr>
				<tr><td>已用Swap内存 : </td><td><span id="UsedSwap">[n/a]</span></td></tr>
				<tr><td>空闲Swap内存 : </td><td><span id="FreeSwap">[n/a]</span></td></tr>
				<tr><td>Swap内存使用率 : </td><td><span id="PercentSwap">[n/a]</span></td></tr>
			</table>
		</div>
		<div id="ServerMysql" class="infoItemDiv">
			<b>MySQL状态</b><br />
			MySQL : <span id="Mysql">[n/a]</span>
		</div>
		<div id="ServerNetWorks" class="infoItemDiv">
			<b>NetWork状态</b><br />
			<div id="ServerNetN">共有 [<span id="NetWorkNum">[n/a]</span>] 个网卡设备</div>
<?php if ($zNetNum>0){?>
			<table>
<?php
	for ($x=0;$x<$zNetNum;++$x)
	{
		echo "\n\t\t\t";
		echo '<tr><td><span id="NetWorkName',$x,'">[n/a]</span> : </td><td>接收 : </td><td><span id="NetWorkInput',$x,'">[n/a]</span> </td><td>发送 : </td><td><span id="NetWorkOut',$x,'">[n/a]</span></td></tr>';
	}
?>
			</table>
<?php }?>
		</div>
		<div id="ServerCONNNum" class="infoItemDiv">
			<b>连接信息</b><br />
			<div id="ServerTCP4N">共有 [<span id="TCP4Num">[n/a]</span>] 个IPV4的TCP连接</div>
			<div id="ServerUDP4N">共有 [<span id="UDP4Num">[n/a]</span>] 个IPV4的UDP连接</div>
			<div id="ServerTCP6N">共有 [<span id="TCP6Num">[n/a]</span>] 个IPV6的TCP连接</div>
			<div id="ServerUDP6N">共有 [<span id="UDP6Num">[n/a]</span>] 个IPV6的UDP连接</div>
		</div>
		<div id="ServerCONNStatus" class="infoItemDiv">
			<b>连接状态</b>
			<table>
				<tr>
					<td></td>
					<td class="scs">IPV4-TCP||</td>
					<td class="scs">IPV4-UDP||</td>
					<td class="scs">IPV6-TCP||</td>
					<td class="scs">IPV6-UDP</td>
				</tr>
				<tr>
					<td>ESTABLISHED : </td>
					<td class="scs"><span id="T4_ESTABLISHED">0</span></td>
					<td class="scs"><span id="U4_ESTABLISHED">0</span></td>
					<td class="scs"><span id="T6_ESTABLISHED">0</span></td>
					<td class="scs"><span id="U6_ESTABLISHED">0</span></td>
				</tr>
				<tr>
					<td>SYN_SENT : </td>
					<td class="scs"><span id="T4_SYN_SENT">0</span></td>
					<td class="scs"><span id="U4_SYN_SENT">0</span></td>
					<td class="scs"><span id="T6_SYN_SENT">0</span></td>
					<td class="scs"><span id="U6_SYN_SENT">0</span></td>
				</tr>
				<tr>
					<td>SYN_RECV : </td>
					<td class="scs"><span id="T4_SYN_RECV">0</span></td>
					<td class="scs"><span id="U4_SYN_RECV">0</span></td>
					<td class="scs"><span id="T6_SYN_RECV">0</span></td>
					<td class="scs"><span id="U6_SYN_RECV">0</span></td>
				</tr>
				<tr>
					<td>FIN_WAIT1 : </td>
					<td class="scs"><span id="T4_FIN_WAIT1">0</span></td>
					<td class="scs"><span id="U4_FIN_WAIT1">0</span></td>
					<td class="scs"><span id="T6_FIN_WAIT1">0</span></td>
					<td class="scs"><span id="U6_FIN_WAIT1">0</span></td>
				</tr>
				<tr>
					<td>FIN_WAIT2 : </td>
					<td class="scs"><span id="T4_FIN_WAIT2">0</span></td>
					<td class="scs"><span id="U4_FIN_WAIT2">0</span></td>
					<td class="scs"><span id="T6_FIN_WAIT2">0</span></td>
					<td class="scs"><span id="U6_FIN_WAIT2">0</span></td>
				</tr>
				<tr>
					<td>TIME_WAIT : </td>
					<td class="scs"><span id="T4_TIME_WAIT">0</span></td>
					<td class="scs"><span id="U4_TIME_WAIT">0</span></td>
					<td class="scs"><span id="T6_TIME_WAIT">0</span></td>
					<td class="scs"><span id="U6_TIME_WAIT">0</span></td>
				</tr>
				<tr>
					<td>CLOSE : </td>
					<td class="scs"><span id="T4_CLOSE">0</span></td>
					<td class="scs"><span id="U4_CLOSE">0</span></td>
					<td class="scs"><span id="T6_CLOSE">0</span></td>
					<td class="scs"><span id="U6_CLOSE">0</span></td>
				</tr>
				<tr>
					<td>CLOSE_WAIT : </td>
					<td class="scs"><span id="T4_CLOSE_WAIT">0</span></td>
					<td class="scs"><span id="U4_CLOSE_WAIT">0</span></td>
					<td class="scs"><span id="T6_CLOSE_WAIT">0</span></td>
					<td class="scs"><span id="U6_CLOSE_WAIT">0</span></td>
				</tr>
				<tr>
					<td>LAST_ACK : </td>
					<td class="scs"><span id="T4_LAST_ACK">0</span></td>
					<td class="scs"><span id="U4_LAST_ACK">0</span></td>
					<td class="scs"><span id="T6_LAST_ACK">0</span></td>
					<td class="scs"><span id="U6_LAST_ACK">0</span></td>
				</tr>
				<tr>
					<td>LISTEN : </td>
					<td class="scs"><span id="T4_LISTEN">0</span></td>
					<td class="scs"><span id="U4_LISTEN">0</span></td>
					<td class="scs"><span id="T6_LISTEN">0</span></td>
					<td class="scs"><span id="U6_LISTEN">0</span></td>
				</tr>
				<tr>
					<td>CLOSING : </td>
					<td class="scs"><span id="T4_CLOSING">0</span></td>
					<td class="scs"><span id="U4_CLOSING">0</span></td>
					<td class="scs"><span id="T6_CLOSING">0</span></td>
					<td class="scs"><span id="U6_CLOSING">0</span></td>
				</tr>
			</table>
		</div>
		<div id="ServerCONNIP" class="infoItemDiv">
			<div id="ServerTCP4IP">
				<b>IPV4的TCP连接</b><br />
				<span id="TCP4conn_1">[n/a]</span><br />
				<span id="TCP4conn_2">[n/a]</span><br />
				<span id="TCP4conn_3">[n/a]</span><br />
				<span id="TCP4conn_4">[n/a]</span><br />
				<span id="TCP4conn_5">[n/a]</span>
			</div>
			<div id="ServerUDP4IP">
				<b>IPV4的UDP连接</b><br />
				<span id="UDP4conn_1">[n/a]</span><br />
				<span id="UDP4conn_2">[n/a]</span><br />
				<span id="UDP4conn_3">[n/a]</span><br />
				<span id="UDP4conn_4">[n/a]</span><br />
				<span id="UDP4conn_5">[n/a]</span>
			</div>
			<div id="ServerTCP6IP">
				<b>IPV6的TCP连接</b><br />
				<span id="TCP6conn_1">[n/a]</span><br />
				<span id="TCP6conn_2">[n/a]</span><br />
				<span id="TCP6conn_3">[n/a]</span><br />
				<span id="TCP6conn_4">[n/a]</span><br />
				<span id="TCP6conn_5">[n/a]</span>
			</div>
			<div id="ServerUDP6IP">
				<b>IPV6的UDP连接</b><br />
				<span id="UDP6conn_1">[n/a]</span><br />
				<span id="UDP6conn_2">[n/a]</span><br />
				<span id="UDP6conn_3">[n/a]</span><br />
				<span id="UDP6conn_4">[n/a]</span><br />
				<span id="UDP6conn_5">[n/a]</span>
			</div>
		</div>
		<div class="clearDiv"></div>
	</div>
	<div id="footer">
		<span id="info">
			<?php
				echo $zMsg,'CopyLeft ',date("Y",time()),' , Powered by <strong>PHP-SYSINFO</strong> <em>v',$zVersion,'</em> .',$zNewVer;
				$runtime->stop();
				echo 'Processed in ',$runtime->spent(),' second(s). Memory Usage ',zFS(memory_get_usage()),'.';
			?>
		</span><br />
		<span id="copyright">
			&copy; CopyRight 2004, <strong><a href="http://www.htooy.org/" rel="external">HTOOY.ORG</a></strong> Inc.All Rights Reserved. | 
			<strong><a href="http://validator.w3.org/check?uri=referer" rel="external">Valid XHTML 1.0 Strict</a></strong> | 
			<strong><a href="http://jigsaw.w3.org/css-validator/check/referer" rel="external">Valid CSS</a></strong>
		</span>
	</div>
	<div class="clearDiv"></div>
</div>
<script type="text/javascript"> 
<!--
$(document).ready(function(){getJSONData();});
function getJSONData()
{
	setTimeout("getJSONData()", <?php echo $zSec;?>);
	$.getJSON('<?php echo $zUrl;?>&callback=?', displayData);
}
function displayData(dataJSON)
{
	$("#key").html(dataJSON.key);
	$("#IP").html(dataJSON.ServerIP);
	$("#num").html(dataJSON.num);
	//ServerloadAvg
	$("#OneLoad").html(dataJSON.ServerloadAvg[0]);
	$("#FiveLoad").html(dataJSON.ServerloadAvg[1]);
	$("#FifteenLoad").html(dataJSON.ServerloadAvg[2]);
	$("#ProcLoad").html(dataJSON.ServerloadAvg[3]);
	//ServerSpace
	$("#FreeSpace").html(dataJSON.ServerSpace.FreeSpace);
	$("#TotalSpace").html(dataJSON.ServerSpace.TotalSpace);
	$("#PercentSpace").html(dataJSON.ServerSpace.PercentSpace + " %");
	//ServerMemory
	$("#TotalMemory").html(dataJSON.ServerMemory.TotalMemory);
	$("#FreeMemory").html(dataJSON.ServerMemory.FreeMemory);
	$("#UsedMemory").html(dataJSON.ServerMemory.UsedMemory);
	$("#CachedMemory").html(dataJSON.ServerMemory.CachedMemory);
	$("#PercentMemory").html(dataJSON.ServerMemory.PercentMemory + " %");
	$("#RealUsedMemory").html(dataJSON.ServerMemory.RealUsedMemory);
	$("#RealPercentMemory").html(dataJSON.ServerMemory.RealPercentMemory + " %");
	//ServerSwap
	$("#TotalSwap").html(dataJSON.ServerSwap.TotalSwap);
	$("#FreeSwap").html(dataJSON.ServerSwap.FreeSwap);
	$("#UsedSwap").html(dataJSON.ServerSwap.UsedSwap);
	$("#PercentSwap").html(dataJSON.ServerSwap.PercentSwap + " %");
	//ServerTime
	$("#UpTime").html(dataJSON.ServerTime.UpTime);
	$("#FreeTime").html(dataJSON.ServerTime.FreeTime);
	$("#BTime").html(dataJSON.ServerTime.BTime);
	$("#BJTime").html(dataJSON.ServerTime.BJTime);
	$("#SysTime").html(dataJSON.ServerTime.SysTime);
	//ServerMysql
	$("#Mysql").html(dataJSON.ServerMysql.SQLStatus);
<?php
if ($zNetNum>0){
?>
	//ServerNetWorks
	$("#NetWorkNum").html(dataJSON.ServerNetWorks.NetWorkNum);
<?php
	for ($y=0;$y<$zNetNum;++$y)
	{
		echo "\t";echo '$("#NetWorkName',$y,'").html(dataJSON.ServerNetWorks.NetName',$y,');';echo "\n\t";
		echo '$("#NetWorkInput',$y,'").html(dataJSON.ServerNetWorks.NetInput',$y,');';echo "\n\t";
		echo '$("#NetWorkOut',$y,'").html(dataJSON.ServerNetWorks.NetOut',$y,');';echo "\n";
	}
}
?>
	//ServerTCP-IPV4
	$("#TCP4Num").html(dataJSON.ServerCONNNum.TCP4Num);
	//ServerUDP-IPV4
	$("#UDP4Num").html(dataJSON.ServerCONNNum.UDP4Num);
	//ServerTCP-IPV6
	$("#TCP6Num").html(dataJSON.ServerCONNNum.TCP6Num);
	//ServerUDP-IPV6
	$("#UDP6Num").html(dataJSON.ServerCONNNum.UDP6Num);
	//ServerTCPconn-IPV4
	$("#TCP4conn_1").html(dataJSON.ServerTCP4conn[0]);
	$("#TCP4conn_2").html(dataJSON.ServerTCP4conn[1]);
	$("#TCP4conn_3").html(dataJSON.ServerTCP4conn[2]);
	$("#TCP4conn_4").html(dataJSON.ServerTCP4conn[3]);
	$("#TCP4conn_5").html(dataJSON.ServerTCP4conn[4]);
	//ServerTCPconn-IPV6
	$("#TCP6conn_1").html(dataJSON.ServerTCP6conn[0]);
	$("#TCP6conn_2").html(dataJSON.ServerTCP6conn[1]);
	$("#TCP6conn_3").html(dataJSON.ServerTCP6conn[2]);
	$("#TCP6conn_4").html(dataJSON.ServerTCP6conn[3]);
	$("#TCP6conn_5").html(dataJSON.ServerTCP6conn[4]);
	//ServerUDPconn-IPV4
	$("#UDP4conn_1").html(dataJSON.ServerUDP4conn[0]);
	$("#UDP4conn_2").html(dataJSON.ServerUDP4conn[1]);
	$("#UDP4conn_3").html(dataJSON.ServerUDP4conn[2]);
	$("#UDP4conn_4").html(dataJSON.ServerUDP4conn[3]);
	$("#UDP4conn_5").html(dataJSON.ServerUDP4conn[4]);
	//ServerUDPconn-IPV6
	$("#UDP6conn_1").html(dataJSON.ServerUDP6conn[0]);
	$("#UDP6conn_2").html(dataJSON.ServerUDP6conn[1]);
	$("#UDP6conn_3").html(dataJSON.ServerUDP6conn[2]);
	$("#UDP6conn_4").html(dataJSON.ServerUDP6conn[3]);
	$("#UDP6conn_5").html(dataJSON.ServerUDP6conn[4]);
	//ServerTCPStatus-IPV4
	$("#T4_ESTABLISHED").html(dataJSON.ServerTCP4Status.ESTABLISHED);
	$("#T4_SYN_SENT").html(dataJSON.ServerTCP4Status.SYN_SENT);
	$("#T4_SYN_RECV").html(dataJSON.ServerTCP4Status.SYN_RECV);
	$("#T4_FIN_WAIT1").html(dataJSON.ServerTCP4Status.FIN_WAIT1);
	$("#T4_FIN_WAIT2").html(dataJSON.ServerTCP4Status.FIN_WAIT2);
	$("#T4_TIME_WAIT").html(dataJSON.ServerTCP4Status.TIME_WAIT);
	$("#T4_CLOSE").html(dataJSON.ServerTCP4Status.CLOSE);
	$("#T4_CLOSE_WAIT").html(dataJSON.ServerTCP4Status.CLOSE_WAIT);
	$("#T4_LAST_ACK").html(dataJSON.ServerTCP4Status.LAST_ACK);
	$("#T4_LISTEN").html(dataJSON.ServerTCP4Status.LISTEN);
	$("#T4_CLOSING").html(dataJSON.ServerTCP4Status.CLOSING);
	//ServerTCPStatus-IPV6
	$("#T6_ESTABLISHED").html(dataJSON.ServerTCP6Status.ESTABLISHED);
	$("#T6_SYN_SENT").html(dataJSON.ServerTCP6Status.SYN_SENT);
	$("#T6_SYN_RECV").html(dataJSON.ServerTCP6Status.SYN_RECV);
	$("#T6_FIN_WAIT1").html(dataJSON.ServerTCP6Status.FIN_WAIT1);
	$("#T6_FIN_WAIT2").html(dataJSON.ServerTCP6Status.FIN_WAIT2);
	$("#T6_TIME_WAIT").html(dataJSON.ServerTCP6Status.TIME_WAIT);
	$("#T6_CLOSE").html(dataJSON.ServerTCP6Status.CLOSE);
	$("#T6_CLOSE_WAIT").html(dataJSON.ServerTCP6Status.CLOSE_WAIT);
	$("#T6_LAST_ACK").html(dataJSON.ServerTCP6Status.LAST_ACK);
	$("#T6_LISTEN").html(dataJSON.ServerTCP6Status.LISTEN);
	$("#T6_CLOSING").html(dataJSON.ServerTCP6Status.CLOSING);
	//ServerUDPStatus-IPV4
	$("#U4_ESTABLISHED").html(dataJSON.ServerUDP4Status.ESTABLISHED);
	$("#U4_SYN_SENT").html(dataJSON.ServerUDP4Status.SYN_SENT);
	$("#U4_SYN_RECV").html(dataJSON.ServerUDP4Status.SYN_RECV);
	$("#U4_FIN_WAIT1").html(dataJSON.ServerUDP4Status.FIN_WAIT1);
	$("#U4_FIN_WAIT2").html(dataJSON.ServerUDP4Status.FIN_WAIT2);
	$("#U4_TIME_WAIT").html(dataJSON.ServerUDP4Status.TIME_WAIT);
	$("#U4_CLOSE").html(dataJSON.ServerUDP4Status.CLOSE);
	$("#U4_CLOSE_WAIT").html(dataJSON.ServerUDP4Status.CLOSE_WAIT);
	$("#U4_LAST_ACK").html(dataJSON.ServerUDP4Status.LAST_ACK);
	$("#U4_LISTEN").html(dataJSON.ServerUDP4Status.LISTEN);
	$("#U4_CLOSING").html(dataJSON.ServerUDP4Status.CLOSING);
	//ServerUDPStatus-IPV6
	$("#U6_ESTABLISHED").html(dataJSON.ServerUDP6Status.ESTABLISHED);
	$("#U6_SYN_SENT").html(dataJSON.ServerUDP6Status.SYN_SENT);
	$("#U6_SYN_RECV").html(dataJSON.ServerUDP6Status.SYN_RECV);
	$("#U6_FIN_WAIT1").html(dataJSON.ServerUDP6Status.FIN_WAIT1);
	$("#U6_FIN_WAIT2").html(dataJSON.ServerUDP6Status.FIN_WAIT2);
	$("#U6_TIME_WAIT").html(dataJSON.ServerUDP6Status.TIME_WAIT);
	$("#U6_CLOSE").html(dataJSON.ServerUDP6Status.CLOSE);
	$("#U6_CLOSE_WAIT").html(dataJSON.ServerUDP6Status.CLOSE_WAIT);
	$("#U6_LAST_ACK").html(dataJSON.ServerUDP6Status.LAST_ACK);
	$("#U6_LISTEN").html(dataJSON.ServerUDP6Status.LISTEN);
	$("#U6_CLOSING").html(dataJSON.ServerUDP6Status.CLOSING);

	if(dataJSON.ServerloadAvg[4] === "1") $("#ServerLoadAvg").show();
	if(dataJSON.ServerloadAvg[4] === "0") $("#ServerLoadAvg").hide();
	if(dataJSON.ServerSpace.Space === "1") $("#ServerSpace").show();
	if(dataJSON.ServerSpace.Space === "0") $("#ServerSpace").hide();
	if(dataJSON.ServerMemory.Mem === "1") $("#ServerMemory").show();
	if(dataJSON.ServerMemory.Mem === "0") $("#ServerMemory").hide();
	if(dataJSON.ServerSwap.Swap === "1") $("#ServerSwap").show();
	if(dataJSON.ServerSwap.Swap === "0") $("#ServerSwap").hide();
	if(dataJSON.ServerTime.UpTime !== "") $("#ServerTime").show();
	if(dataJSON.ServerTime.UpTime === "") $("#ServerTime").hide();
	if(dataJSON.ServerStatus.SQL === "1") $("#ServerMysql").show();
	if(dataJSON.ServerStatus.SQL === "0") $("#ServerMysql").hide();
	if(dataJSON.ServerStatus.Net === "1") $("#ServerNetWorks").show();
	if(dataJSON.ServerStatus.Net === "0") $("#ServerNetWorks").hide();
	if(dataJSON.ServerNetWorks.NetWorkNum > "0") $("#ServerNetN").show();
	if(dataJSON.ServerNetWorks.NetWorkNum === "0") $("#ServerNetN").hide();

	if((dataJSON.ServerStatus.T4N === "1")||(dataJSON.ServerStatus.U4N === "1")||(dataJSON.ServerStatus.T6N === "1")||(dataJSON.ServerStatus.U6N === "1")) $("#ServerCONNNum").show();
	if((dataJSON.ServerStatus.T4N === "0")&&(dataJSON.ServerStatus.U4N === "0")&&(dataJSON.ServerStatus.T6N === "0")&&(dataJSON.ServerStatus.U6N === "0")) $("#ServerCONNNum").hide();
	if((dataJSON.ServerCONNNum.TCP4Num > "0")||(dataJSON.ServerCONNNum.UDP4Num > "0")||(dataJSON.ServerCONNNum.TCP6Num > "0")||(dataJSON.ServerCONNNum.UDP6Num > "0")) $("#ServerCONNNum").show();
	if((dataJSON.ServerCONNNum.TCP4Num === "0")&&(dataJSON.ServerCONNNum.UDP4Num === "0")&&(dataJSON.ServerCONNNum.TCP6Num === "0")&&(dataJSON.ServerCONNNum.UDP6Num === "0")) $("#ServerCONNNum").hide();
	if(dataJSON.ServerCONNNum.TCP4Num > "0") $("#ServerTCP4N").show();
	if(dataJSON.ServerCONNNum.TCP4Num === "0") $("#ServerTCP4N").hide();
	if(dataJSON.ServerCONNNum.UDP4Num > "0") $("#ServerUDP4N").show();
	if(dataJSON.ServerCONNNum.UDP4Num === "0") $("#ServerUDP4N").hide();
	if(dataJSON.ServerCONNNum.TCP6Num > "0") $("#ServerTCP6N").show();
	if(dataJSON.ServerCONNNum.TCP6Num === "0") $("#ServerTCP6N").hide();
	if(dataJSON.ServerCONNNum.UDP6Num > "0") $("#ServerUDP6N").show();
	if(dataJSON.ServerCONNNum.UDP6Num === "0") $("#ServerUDP6N").hide();

	if((dataJSON.ServerStatus.T4ST === "1")||(dataJSON.ServerStatus.U4ST === "1")||(dataJSON.ServerStatus.T6ST === "1")||(dataJSON.ServerStatus.U6ST === "1")) $("#ServerCONNStatus").show();
	if((dataJSON.ServerStatus.T4ST === "0")&&(dataJSON.ServerStatus.U4ST === "0")&&(dataJSON.ServerStatus.T6ST === "0")&&(dataJSON.ServerStatus.U6ST === "0")) $("#ServerCONNStatus").hide();
	if(dataJSON.ServerStatus.T4ST === "1") $("#ServT4ST").show();
	if(dataJSON.ServerStatus.T4ST === "0") $("#ServT4ST").hide();
	if(dataJSON.ServerStatus.U4ST === "1") $("#ServU4ST").show();
	if(dataJSON.ServerStatus.U4ST === "0") $("#ServU4ST").hide();
	if(dataJSON.ServerStatus.T6ST === "1") $("#ServT6ST").show();
	if(dataJSON.ServerStatus.T6ST === "0") $("#ServT6ST").hide();
	if(dataJSON.ServerStatus.U6ST === "1") $("#ServU6ST").show();
	if(dataJSON.ServerStatus.U6ST === "0") $("#ServU6ST").hide();

	if((dataJSON.ServerStatus.T4IP === "1")||(dataJSON.ServerStatus.U4IP == "1")||(dataJSON.ServerStatus.T6IP == "1")||(dataJSON.ServerStatus.U6IP == "1")) $("#ServerCONNIP").show();
	if((dataJSON.ServerStatus.T4IP === "0")&&(dataJSON.ServerStatus.U4IP == "0")&&(dataJSON.ServerStatus.T6IP == "0")&&(dataJSON.ServerStatus.U6IP == "0")) $("#ServerCONNIP").hide();
	if(dataJSON.ServerStatus.T4IP === "1") $("#ServerTCP4IP").show();
	if(dataJSON.ServerStatus.T4IP === "0") $("#ServerTCP4IP").hide();
	if(dataJSON.ServerStatus.U4IP === "1") $("#ServerUDP4IP").show();
	if(dataJSON.ServerStatus.U4IP === "0") $("#ServerUDP4IP").hide();
	if(dataJSON.ServerStatus.T6IP === "1") $("#ServerTCP6IP").show();
	if(dataJSON.ServerStatus.T6IP === "0") $("#ServerTCP6IP").hide();
	if(dataJSON.ServerStatus.U6IP === "1") $("#ServerUDP6IP").show();
	if(dataJSON.ServerStatus.U6IP === "0") $("#ServerUDP6IP").hide();
	if(dataJSON.ServerTCP4conn[0] !== "") $("#ServerTCP4IP").show();
	if(dataJSON.ServerTCP4conn[0] === "") $("#ServerTCP4IP").hide();
	if(dataJSON.ServerUDP4conn[0] !== "") $("#ServerUDP4IP").show();
	if(dataJSON.ServerUDP4conn[0] === "") $("#ServerUDP4IP").hide();
	if(dataJSON.ServerTCP6conn[0] !== "") $("#ServerTCP6IP").show();
	if(dataJSON.ServerTCP6conn[0] === "") $("#ServerTCP6IP").hide();
	if(dataJSON.ServerUDP6conn[0] !== "") $("#ServerUDP6IP").show();
	if(dataJSON.ServerUDP6conn[0] === "") $("#ServerUDP6IP").hide();

	if(dataJSON.ServerloadAvg[0] > "<?php echo $zOL;?>") jQuery.noticeAdd({text: '<?php echo $zFLT ;?>',stay: false,type: 'error'});
	if(dataJSON.ServerloadAvg[1] > "<?php echo $zFL;?>") jQuery.noticeAdd({text: '<?php echo $zFtLT ;?>',stay: false,type: 'error'});
	if(dataJSON.ServerloadAvg[2] > "<?php echo $zFtL;?>") jQuery.noticeAdd({text: '<?php echo $zDPT ;?>',stay: false,type: 'error'});
	if(dataJSON.ServerSpace.PercentSpace > "<?php echo $zDP;?>") jQuery.noticeAdd({text: '<?php echo $zOLT ;?>',stay: false,type: 'error'});
	if(dataJSON.ServerMemory.RealPercentMemory > "<?php echo $zMRP;?>") jQuery.noticeAdd({text: '<?php echo $zMRPT ;?>',stay: false,type: 'error'});
	if(dataJSON.ServerSwap.PercentSwap > "<?php echo $zSP;?>") jQuery.noticeAdd({text: '<?php echo $zSPT ;?>',stay: false,type: 'error'});


/*
jQuery.noticeAdd({text: '111111111',stay: false});
jQuery.noticeAdd({text: '444444444',stay: false,type: 'error'});
jQuery.noticeAdd({text: '555555555',stay: false,type: 'success'});
$('.remove').click(function(){jQuery.noticeRemove($('.notice-item-wrapper'), 400);});
*/

}
-->
</script>
</body>
</html>
<?php
function zFS($fileSize)
{
	$unit = array(' Bytes', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB');
	$i = 0;
	//由于计算机做乘法比做除法快
	$inv = 1 / 1024;
	while($fileSize >= 1024 && $i < 8)
	{
		$fileSize *= $inv;
		++$i;
	}
	$fileSizeTmp = sprintf("%.2f", $fileSize);
	//以下代码在99.99%的情况下结果会是正确的，除非你使用了"超超大数"。：）
	return ($fileSizeTmp - (int)$fileSizeTmp ? $fileSizeTmp : $fileSize) . $unit[$i];
}
function get_http_response_code($theURL)
{
	$headers = get_headers($theURL);
	return substr($headers[0], 9, 3);
}
?>